<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

#[Hateoas\Relation(
    name: 'self1',
    href: 'foo1',
    embedded: 'foo1',
)]
final class Foo1
{
    public mixed $inline;
}
