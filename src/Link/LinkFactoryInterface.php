<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Link;

use InvalidArgumentException;
use RuntimeException;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Model\Link;

interface LinkFactoryInterface
{
    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function createLink(object $object, Relation $relation): Link;
}
