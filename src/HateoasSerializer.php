<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas;

use Symfony\Component\Serializer\SerializerInterface;

final class HateoasSerializer implements HateoasSerializerInterface
{
    public function __construct(
        public readonly SerializerInterface $decoratedSerializer
    ) {}

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->decoratedSerializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return $this->decoratedSerializer->deserialize($data, $type, $format, $context);
    }
}
