<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Loader;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
use Zuruuh\Hateoas\ClassMetadata\HateoasClassMetadataInterface;
use Zuruuh\Hateoas\Expression\ExpressionEvaluatorInterface;
use Zuruuh\Hateoas\Mapping\Embedded;
use Zuruuh\Hateoas\Mapping\Exclusion;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Mapping\RelationProvider;
use Zuruuh\Hateoas\Mapping\Route;

final class AttributeMappingLoader implements LoaderInterface
{
    public function __construct(
        private readonly ExpressionEvaluatorInterface $expressionEvaluator,
        private readonly LoaderInterface $decoratedLoader,
    ) {}

    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        $this->decoratedLoader->loadClassMetadata($classMetadata);

        $attributes = $classMetadata->getReflectionClass()->getAttributes();

        if ($attributes === []) {
            return false;
        }

        foreach ($attributes as $reflectionAttribute) {
            $attribute = $attribute->newInstance();

            if ($attribute instanceof Relation) {
                $classMetadata->addAttributeMetadata(new Relation(
                    $attribute->name,
                    $this->createHref($attribute->href),
                    $this->createEmbedded($attribute->embedded),
                    $this->expressionEvaluator->tryToEvaluateExpressionArray($attribute->attributes) ?: [],
                    $this->createExclusion($attribute->exclusion)
                ));
            } elseif ($attribute instanceof RelationProvider) {
                $class = $attribute->class ?? $classMetadata->getReflectionClass()->getName();
                $magicMethod = "{$class}::{$attribute->method}";
                assert(is_callable($magicMethod));
                $relations = $magicMethod();

                foreach ($relations as $relation) {
                    $classMetadata->addRelation($relation);
                }
            }
        }

        return $classMetadata->getRelations() !== [];
    }

    private function parseExclusion(Exclusion $exclusion): Exclusion
    {
        return new Exclusion(
            $exclusion->groups,
            null !== $exclusion->sinceVersion ? (string) $exclusion->sinceVersion : null,
            null !== $exclusion->untilVersion ? (string) $exclusion->untilVersion : null,
            null !== $exclusion->maxDepth ? (int) $exclusion->maxDepth : null,
            $this->expressionEvaluator->evaluateExpressionIfNeeded($exclusion->excludeIf)
        );
    }

    /**
     * @return Route|mixed
     */
    private function createHref(mixed $href): mixed
    {
        if ($href instanceof Route) {
            return new Route(
                $this->expressionEvaluator->evaluateExpressionIfNeeded($href->name),
                is_array($href->parameters)
                    ? $this->expressionEvaluator->evaluateArrayOfExpressionsIfNeeded($href->parameters)
                    : $this->expressionEvaluator->evaluateExpressionIfNeeded($href->parameters),
                $this->expressionEvaluator->evaluateExpressionIfNeeded($href->absolute),
                $href->generator
            );
        }

        return $this->expressionEvaluator->evaluateExpressionIfNeeded($href);
    }

    /**
     * @param Annotation\Embedded|mixed $embedded
     *
     * @return Expression|mixed
     */
    private function createEmbedded($embedded)
    {
        if ($embedded instanceof Embedded) {
            $embeddedExclusion = $embedded->exclusion;

            if (null !== $embeddedExclusion) {
                $embeddedExclusion = $this->parseExclusion($embeddedExclusion);
            }

            return new Embedded(
                $this->expressionEvaluator->evaluateExpressionIfNeeded($embedded->content),
                $this->expressionEvaluator->evaluateExpressionIfNeeded($embedded->xmlElementName),
                $embeddedExclusion,
                null !== $embedded->type ? $this->typeParser->parse($embedded->type) : null
            );
        }

        return $this->expressionEvaluator->evaluateExpressionIfNeeded($embedded);
    }

    private function createExclusion(Exclusion $exclusion = null): ?Exclusion
    {
        if (null !== $exclusion) {
            $exclusion = $this->parseExclusion($exclusion);
        }

        return $exclusion;
    }
}
