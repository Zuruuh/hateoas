<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Relation;

use Zuruuh\Hateoas\Mapping\Relation;

interface RelationsProviderInterface
{
    /**
     * @return list<Relation> list of Relations for specified object
     */
    public static function getRelations(): array;
}
