<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata\Loader;

use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;

final class AttributeMappingLoader implements LoaderInterface
{
    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        // TODO: implement
        return true;
    }
}
