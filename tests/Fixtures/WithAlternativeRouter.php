<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Relation\RelationsProviderInterface;
use Zuruuh\Hateoas\Mapping as Hateoas;

final class WithAlternativeRouter implements RelationsProviderInterface {
    /**
     * {@inheritDoc}
     */
    #[Hateoas\RelationProvider]
    public static function getRelations(): array
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
            )
        ];
    }
}
