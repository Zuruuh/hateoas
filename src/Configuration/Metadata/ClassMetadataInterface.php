<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata;

use Zuruuh\Hateoas\Configuration\Relation;

interface ClassMetadataInterface
{
    public function getName(): string;

    /**
     * @return Relation[]
     */
    public function getRelations(): array;

    public function addRelation(Relation $relation): void;
}
