<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Exclusion;

use Zuruuh\Hateoas\Mapping\Relation;

interface ExclusionManagerInterface
{
    public function shouldSkipLink(object $object, Relation $relation): bool;
}
