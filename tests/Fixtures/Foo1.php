<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

final class Foo1
{
    #[Hateoas\Relation(
        name: 'self1',
        href: 'foo1',
        embedded: 'foo1',
    )]
    public mixed $inline;
}
