<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Normalizer;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zuruuh\Hateoas\Expression\ExpressionEvaluatorInterface;
use Zuruuh\Hateoas\Loader\AttributeMappingLoader;
use Zuruuh\Hateoas\Mapping\Embedded;
use Zuruuh\Hateoas\Mapping\Exclusion;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Mapping\Route;
use Zuruuh\Hateoas\Relation\RelationMetadata;

final class HypermediaNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly ClassMetadataFactoryInterface $classMetadataFactory,
        private readonly NormalizerInterface $normalizer,
        private readonly ExpressionEvaluatorInterface $expressionEvaluator,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function normalize(
        mixed $object,
        string $format = null,
        array $context = []
    ): array|string|int|float|bool|\ArrayObject|null {
        $normalized = $this->normalizer->normalize($object, $format, $context);

        if (
            !$this->classMetadataFactory->hasMetadataFor($object)
            || !is_array($normalized)
            || !is_object($object)
        ) {
            return $normalized;
        }

        $metadata = $this->classMetadataFactory->getMetadataFor($object);

        $relationsMetadata = $metadata->getAttributesMetadata()[AttributeMappingLoader::INTERNAL_RELATION_METADATA_KEY] ?? null;
        if (!$relationsMetadata instanceof RelationMetadata) {
            return $normalized;
        }

        foreach ($relationsMetadata->relations as $relation) {
            assert($relation instanceof Relation);

            $normalized['_links'][$relation->name] = [
                'href' => $relation->href,
            ];
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return is_object($data);
    }

    private function expandRelationAttribute(Relation $relation): Relation
    {
        return new Relation(
            $relation->name,
            $this->createHref($relation->href),
            $this->createEmbedded($relation->embedded),
            $this->expressionEvaluator->tryToEvaluateExpressionArray($relation->attributes) ?: [],
            $this->createExclusion($relation->exclusion)
        );
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
     * @template T
     *
     * @param T $href
     *
     * @return T is Route ? Route :  mixed
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
     * @param Embedded|mixed $embedded
     *
     * @return Expression|mixed
     */
    private function createEmbedded(mixed $embedded): mixed
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
