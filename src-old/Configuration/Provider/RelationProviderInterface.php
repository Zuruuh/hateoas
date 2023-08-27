<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Provider;

use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\RelationProvider;

interface RelationProviderInterface
{
    /**
     * @return Relation[] returns array of Relations for specified object
     */
    public function getRelations(RelationProvider $relationProvider, string $class): array;
}
