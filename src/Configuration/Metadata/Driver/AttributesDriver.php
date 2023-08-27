<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata\Driver;

use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Zuruuh\Hateoas\Configuration\Annotation;
use Zuruuh\Hateoas\Configuration\Embedded;
use Zuruuh\Hateoas\Configuration\Exclusion;
use Zuruuh\Hateoas\Configuration\Metadata\ClassMetadataWithRelations;
use Zuruuh\Hateoas\Configuration\Provider\RelationProviderInterface;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\RelationProvider;
use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Resolver\ExpressionLanguageResolver;

final class AttributesDriver implements ClassMetadataFactoryInterface
{
    public function __construct(
        private RelationProviderInterface $relationProvider,
        private ExpressionLanguageResolver $expressionLanguageResolver,
        private ClassMetadataFactoryInterface $classMetadataFactory,
    ) {}

    public function getMetadataFor(string|object $value): ClassMetadataInterface
    {
        $classMetadata = new ClassMetadataWithRelations($this->classMetadataFactory->getMetadataFor($value));
        $attributes = $classMetadata->getReflectionClass()->getAttributes();

        if (count($attributes) === 0) {
            return $classMetadata;
        }

        foreach ($attributes as $attribute) {
            if ($attribute instanceof Annotation\Relation) {
                $classMetadata->addRelation(new Relation(
                    $attribute->name,
                    $this->createHref($attribute->href),
                    $this->createEmbedded($attribute->embedded),
                    [],
                    // TODO: Find a workaround
                    // $this->checkExpressionArray($attribute->attributes) ?: [],
                    $this->createExclusion($attribute->exclusion)
                ));
            } elseif ($attribute instanceof Annotation\RelationProvider) {
                $classMetadata->addRelation(
                    $this->relationProvider->getRelations(
                        new RelationProvider($attribute->name),
                        $classMetadata->getName(),
                    )
                );
            }
        }

        return $classMetadata;
    }

    private function parseExclusion(Annotation\Exclusion $exclusion): Exclusion
    {
        return new Exclusion(
            $exclusion->groups,
            null !== $exclusion->sinceVersion ? (string) $exclusion->sinceVersion : null,
            null !== $exclusion->untilVersion ? (string) $exclusion->untilVersion : null,
            null !== $exclusion->maxDepth ? (int) $exclusion->maxDepth : null,
            $this->checkExpression($exclusion->excludeIf)
        );
    }

    /**
     * @param mixed $href
     *
     * @return Expression|mixed
     */
    private function createHref($href)
    {
        if ($href instanceof Annotation\Route) {
            return new Route(
                $this->checkExpression($href->name),
                is_array($href->parameters) ? $this->checkExpressionArray($href->parameters) : $this->checkExpression($href->parameters),
                $this->checkExpression($href->absolute),
                $href->generator
            );
        }

        return $this->checkExpression($href);
    }

    /**
     * @param Annotation\Embedded|mixed $embedded
     *
     * @return Expression|mixed
     */
    private function createEmbedded($embedded)
    {
        if ($embedded instanceof Annotation\Embedded) {
            $embeddedExclusion = $embedded->exclusion;

            if (null !== $embeddedExclusion) {
                $embeddedExclusion = $this->parseExclusion($embeddedExclusion);
            }

            return new Embedded(
                $this->checkExpression($embedded->content),
                $this->checkExpression($embedded->xmlElementName),
                $embeddedExclusion,
                null !== $embedded->type ? $this->typeParser->parse($embedded->type) : null
            );
        }

        return $this->checkExpression($embedded);
    }

    private function createExclusion(?Annotation\Exclusion $exclusion = null): ?Exclusion
    {
        if (null !== $exclusion) {
            $exclusion = $this->parseExclusion($exclusion);
        }

        return $exclusion;
    }

    public function hasMetadataFor(mixed $value): bool
    {
    }
}
