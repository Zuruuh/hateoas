<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Normalizer;

use ArrayObject;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ChainNormalizer implements NormalizerInterface
{
    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function __construct(private readonly iterable $normalizers) {}

    /**
     * {@inheritDoc}
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supportsNormalization($object, $format)) {
                return $normalizer->normalize($object, $format, $context);
            }
        }

        throw new UnsupportedException("This normalizer does not support normalization for format {$format}");
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supportsNormalization($data, $format)) {
                return true;
            }
        }

        return false;
    }
}
