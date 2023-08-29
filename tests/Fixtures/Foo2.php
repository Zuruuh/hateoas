<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

#[Hateoas\Relation(
    name: 'self2',
    href: 'foo2',
    embedded: 'foo2',
)]
final class Foo2
{
    public mixed $inline;
}
