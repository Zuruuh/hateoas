<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\ClassMetadata\Factory;

use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Zuruuh\Hateoas\ClassMetadata\HateoasClassMetadataInterface;

interface HateoasClassMetadataFactoryInterface extends ClassMetadataFactoryInterface
{
    /**
     * @return HateoasClassMetadataInterface
     */
    public function getMetadataFor(string|object $value): ClassMetadataInterface;
}
