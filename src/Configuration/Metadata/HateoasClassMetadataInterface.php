<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata;

use Hateoas\Configuration\Relation;

interface HateoasClassMetadataInterface
{
    /**
     * @return list<Relation>
     */
    public function getRelations(): array;

    public function addRelation(Relation $relation): void;
}
