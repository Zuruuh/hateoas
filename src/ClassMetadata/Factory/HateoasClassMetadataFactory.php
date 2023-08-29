<?php

namespace Zuruuh\Hateoas\ClassMetadata\Factory;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Zuruuh\Hateoas\ClassMetadata\HateoasClassMetadata;
use Zuruuh\Hateoas\Configuration\Metadata\HateoasClassMetadataInterface;

final class HateoasClassMetadataFactory implements HateoasClassMetadataFactoryInterface
{
    public function __construct(private readonly ClassMetadataFactoryInterface $classMetadataFactory) {}

    public function getMetadataFor(string|object $value): HateoasClassMetadataInterface
    {
        return new HateoasClassMetadata($this->classMetadataFactory->getMetadataFor($value));
    }

    public function hasMetadataFor(mixed $value): bool
    {
        return $this->classMetadataFactory->hasMetadataFor($value);
    }
}
