<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

final class Foo2
{
    #[Hateoas\Relation(
        name: 'self2',
        href: 'foo2',
        embedded: 'foo2',
    )]
    public mixed $inline;
}
