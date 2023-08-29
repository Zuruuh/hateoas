<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\ClassMetadata\Factory;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Zuruuh\Hateoas\ClassMetadata\HateoasClassMetadataInterface;

interface HateoasClassMetadataFactoryInterface extends ClassMetadataFactoryInterface
{
    public function getMetadataFor(string|object $value): HateoasClassMetadataInterface;
}
