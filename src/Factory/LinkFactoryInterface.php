<?php

namespace Zuruuh\Hateoas\Factory;

use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Model\Link;

interface LinkFactoryInterface
{
    public function createLink(object $object, Relation $relation): Link;
}
