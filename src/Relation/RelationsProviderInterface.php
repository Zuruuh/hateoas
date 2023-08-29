<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Relation;

use Zuruuh\Hateoas\Mapping\Relation;

interface RelationsProviderInterface
{
    /**
     * @return list<Relation>
     */
    public static function getRelations(): array;
}
