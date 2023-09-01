<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;
use Zuruuh\Hateoas\Relation\RelationsProviderInterface;

final class WithAlternativeRouter implements RelationsProviderInterface
{
    #[Hateoas\RelationProvider]
    public static function provideHypermediaRelations(): array
    {
        return [
            new Hateoas\Relation(
                name: 'search',
                href: new Hateoas\Route(
                    name: '/search',
                    parameters: [
                        'query' => 'hello',
                    ],
                    generator: 'my_generator',
                ),
            ),
        ];
    }
}
