<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

#[Hateoas\Relation(
    name: 'self3',
    href: 'foo3',
    embedded: 'foo3',
)]
final class Foo3 {}
