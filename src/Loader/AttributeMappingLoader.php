<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Loader;

use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Mapping\RelationProvider;
use Zuruuh\Hateoas\Relation\RelationMetadata;

final class AttributeMappingLoader implements LoaderInterface
{
    /**
     * @internal
     */
    public const INTERNAL_RELATION_METADATA_KEY = '_relations';

    public function __construct(
        private readonly LoaderInterface $decoratedLoader,
    ) {}

    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        $relations = [];
        $this->decoratedLoader->loadClassMetadata($classMetadata);

        $classAttributes = $classMetadata->getReflectionClass()->getAttributes();

        foreach ($classAttributes as $classAttribute) {
            if (is_subclass_of($classAttribute->getName(), Relation::class)) {
                $relation = $classAttribute->newInstance();
                assert($relation instanceof Relation);

                $relations[] = $relation;
            }
        }

        $reflectionMethods = $classMetadata->getReflectionClass()->getMethods();

        foreach ($reflectionMethods as $reflectionMethod) {
            foreach ($reflectionMethod->getAttributes() as $attribute) {
                $attributeName = $attribute->getName();

                if (is_subclass_of($attributeName, Relation::class)) {
                    $relation = $attribute->newInstance();
                    assert($relation instanceof Relation);

                    $relations[] = $relation;

                    continue;
                }

                if (is_subclass_of($attributeName, RelationProvider::class) || $attributeName === RelationProvider::class) {
                    $providedRelations = $reflectionMethod->invoke(null);
                    assert(is_array($providedRelations));

                    foreach ($providedRelations as $relation) {
                        assert($relation instanceof Relation);
                        $relations[] = $relation;
                    }
                }
            }
        }

        $attributeMetadata = new RelationMetadata(new AttributeMetadata(self::INTERNAL_RELATION_METADATA_KEY), $relations);
        $classMetadata->addAttributeMetadata($attributeMetadata);

        return $relations !== [];
    }
    /*  */
    /* private function expandRelationAttribute(Relation $relation): Relation */
    /* { */
    /*     return new Relation( */
    /*         $relation->name, */
    /*         $this->createHref($relation->href), */
    /*         $this->createEmbedded($relation->embedded), */
    /*         $this->expressionEvaluator->tryToEvaluateExpressionArray($relation->attributes) ?: [], */
    /*         $this->createExclusion($relation->exclusion) */
    /*     ); */
    /* } */
    /*  */
    /* private function parseExclusion(Exclusion $exclusion): Exclusion */
    /* { */
    /*     return new Exclusion( */
    /*         $exclusion->groups, */
    /*         null !== $exclusion->sinceVersion ? (string) $exclusion->sinceVersion : null, */
    /*         null !== $exclusion->untilVersion ? (string) $exclusion->untilVersion : null, */
    /*         null !== $exclusion->maxDepth ? (int) $exclusion->maxDepth : null, */
    /*         $this->expressionEvaluator->evaluateExpressionIfNeeded($exclusion->excludeIf) */
    /*     ); */
    /* } */

    /**
     * @return Route|mixed
     */
    /* private function createHref(mixed $href): mixed */
    /* { */
    /*     if ($href instanceof Route) { */
    /*         return new Route( */
    /*             $this->expressionEvaluator->evaluateExpressionIfNeeded($href->name), */
    /*             is_array($href->parameters) */
    /*                 ? $this->expressionEvaluator->evaluateArrayOfExpressionsIfNeeded($href->parameters) */
    /*                 : $this->expressionEvaluator->evaluateExpressionIfNeeded($href->parameters), */
    /*             $this->expressionEvaluator->evaluateExpressionIfNeeded($href->absolute), */
    /*             $href->generator */
    /*         ); */
    /*     } */

        /* return $this->expressionEvaluator->evaluateExpressionIfNeeded($href); */
    /* } */

    /**
     * @param Annotation\Embedded|mixed $embedded
     *
     * @return Expression|mixed
     */
    /* private function createEmbedded($embedded) */
    /* { */
    /*     if ($embedded instanceof Embedded) { */
    /*         $embeddedExclusion = $embedded->exclusion; */
            /*  */
            /* if (null !== $embeddedExclusion) { */
            /*     $embeddedExclusion = $this->parseExclusion($embeddedExclusion); */
            /* } */
        /*  */
        /*     return new Embedded( */
        /*         $this->expressionEvaluator->evaluateExpressionIfNeeded($embedded->content), */
        /*         $this->expressionEvaluator->evaluateExpressionIfNeeded($embedded->xmlElementName), */
        /*         $embeddedExclusion, */
        /*         null !== $embedded->type ? $this->typeParser->parse($embedded->type) : null */
        /*     ); */
        /* } */

    /*     return $this->expressionEvaluator->evaluateExpressionIfNeeded($embedded); */
    /* } */
    /*  */
    /* private function createExclusion(Exclusion $exclusion = null): ?Exclusion */
    /* { */
    /*     if (null !== $exclusion) { */
    /*         $exclusion = $this->parseExclusion($exclusion); */
    /*     } */
/*  */
/*         return $exclusion; */
/*     } */
}
