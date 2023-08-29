<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

#[Hateoas\Relation(
    name: 'search',
    href: new Hateoas\Route(
        name: '/search',
        parameters: [
            'query' => 'hello',
        ],
        generator: 'my_generator',
    ),
)]
final class WithAlternativeRouter {}
