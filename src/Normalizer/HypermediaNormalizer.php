<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Normalizer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zuruuh\Hateoas\Loader\AttributeMappingLoader;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Relation\RelationMetadata;

final class HypermediaNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly ClassMetadataFactoryInterface $classMetadataFactory,
        private readonly NormalizerInterface $normalizer,
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

        if (!$this->classMetadataFactory->hasMetadataFor($object) || !is_array($normalized)) {
            return $normalized;
        }

        assert(is_object($object));
        $metadata = $this->classMetadataFactory->getMetadataFor($object);

        $relationsMetadata = $metadata->getAttributesMetadata()[AttributeMappingLoader::INTERNAL_RELATION_METADATA_KEY] ?? null;
        if (!$relationsMetadata instanceof RelationMetadata) {
            return $normalized;
        }

        foreach ($relationsMetadata->relations as $relation) {
            assert($relation instanceof Relation);
            /* dump($relation); */

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
}
