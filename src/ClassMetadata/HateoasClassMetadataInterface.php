<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\ClassMetadata;

use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Zuruuh\Hateoas\Mapping\Relation;

interface HateoasClassMetadataInterface extends ClassMetadataInterface
{
    /**
     * @return list<Relation>
     */
    public function getRelations(): array;

    public function addRelation(Relation $relation): void;
}
