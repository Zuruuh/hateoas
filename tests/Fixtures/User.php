<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

#[Hateoas\Relation(
    name: 'self',
    href: 'http://hateoas.web/users/42',
    attributes: ['type' => 'application/json']
)]
#[Hateoas\Relation(
    name: 'foo',
    href: new Hateoas\Route(
        name: 'user_get',
        parameters: ['id' => 'expr(object.getId())']
    ),
    embedded: 'expr(object.getFoo())'
)]
#[Hateoas\Relation(
    name: 'bar',
    href: 'foo',
    embedded: new Hateoas\Embedded(
        content: 'data',
        nodeName: 'barTag',
    )
)]
#[Hateoas\Relation(
    name: 'baz',
    href: new Hateoas\Route(
        name: 'user_get',
        parameters: ['id' => 'expr(object.getId())'],
        absolute: true,
    ),
    embedded: 'expr(object.getFoo())',
)]
#[Hateoas\Relation(
    name: 'boom',
    href: new Hateoas\Route(
        name: 'user_get',
        parameters: ['id' => 'expr(object.getId())'],
        absolute: false
    ),
    embedded: 'expr(object.getFoo())',
)]
#[Hateoas\Relation('badaboom', embedded: 'expr(object.getFoo())')]
#[Hateoas\Relation(
    name: 'hello',
    href: '/hello',
    exclusion: new Hateoas\Exclusion(
        groups: ['group1', 'group2'],
        sinceVersion: '1',
        untilVersion: '2.2',
        maxDepth: 42,
        excludeIf: 'foo'
    ),
    embedded: new Hateoas\Embedded(
        content: 'hello',
        nodeName: 'barTag',
        type: 'string',
        exclusion: new Hateoas\Exclusion(
            groups: ['group3', 'group4'],
            sinceVersion: '1.1',
            untilVersion: '2.3',
            maxDepth: 43,
            excludeIf: 'bar'
        )
    )
)]
#[Hateoas\Relation(
    name: 'attribute_with_expression',
    href: 'baz',
    attributes: ['baz' => 'expr(object.getId())']
)]
#[Hateoas\RelationProvider(
    method: 'getRelations',
)]
final class User
{
    /**
     * do not use for functional testing.
     *
     * @return array{}
     */
    public static function getRelations(): array
    {
        return [];
    }
}
