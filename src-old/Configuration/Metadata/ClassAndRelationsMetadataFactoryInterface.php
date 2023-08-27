<?php

namespace Zuruuh\Hateoas\Configuration\Metadata;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

interface ClassAndRelationsMetadataFactoryInterface extends ClassMetadataFactoryInterface
{
    public function getMetadataFor(string|object $value): ClassMetadataWithRelations;
}
