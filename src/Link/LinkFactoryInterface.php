<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Link;

use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Model\Link;

interface LinkFactoryInterface
{
    public function createLink(object $object, Relation $relation): Link;
}
