<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata\Driver;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Zuruuh\Hateoas\Configuration\Annotation;
use Zuruuh\Hateoas\Configuration\Embedded;
use Zuruuh\Hateoas\Configuration\Exclusion;
use Zuruuh\Hateoas\Configuration\Metadata\ClassAndRelationsMetadataFactoryInterface;
use Zuruuh\Hateoas\Configuration\Metadata\ClassMetadataWithRelations;
use Zuruuh\Hateoas\Configuration\Provider\RelationProviderInterface;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\RelationProvider;
use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Resolver\ExpressionLanguageResolver;

final class AttributesDriver implements ClassAndRelationsMetadataFactoryInterface
{
    public $typeParser;

    public function __construct(
        private readonly RelationProviderInterface $relationProvider,
        private readonly ExpressionLanguageResolver $expressionLanguageResolver,
        private readonly ClassMetadataFactoryInterface $classMetadataFactory,
    ) {}

    public function getMetadataFor(string|object $value): ClassMetadataWithRelations
    {
        $classMetadata = new ClassMetadataWithRelations($this->classMetadataFactory->getMetadataFor($value));
        $attributes = $classMetadata->getReflectionClass()->getAttributes();

        if ([] === $attributes) {
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

    public function hasMetadataFor(mixed $value): bool {}

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

            if ($embeddedExclusion instanceof \Hateoas\Configuration\Annotation\Exclusion) {
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
        if ($exclusion instanceof \Zuruuh\Hateoas\Configuration\Annotation\Exclusion) {
            $exclusion = $this->parseExclusion($exclusion);
        }

        return $exclusion;
    }
}
