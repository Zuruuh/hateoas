<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Link;

use Zuruuh\Hateoas\Model\Link;

interface LinksFactoryInterface
{
    /**
     * @return list<Link>
     */
    public function create(object $object): array;
}
