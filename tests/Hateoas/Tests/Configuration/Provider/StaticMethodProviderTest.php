<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Provider;

use Zuruuh\Hateoas\Configuration\Provider\StaticMethodProvider;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\RelationProvider;
use Zuruuh\Hateoas\Tests\TestCase;

class StaticMethodProviderTest extends TestCase
{
    public function test(): void
    {
        $providerProvider = new StaticMethodProvider();

        $this->assertEmpty($providerProvider->getRelations(new RelationProvider('!-;'), \stdClass::class));
        $this->assertEmpty($providerProvider->getRelations(new RelationProvider('getSomething'), \stdClass::class));
        $this->assertEmpty($providerProvider->getRelations(new RelationProvider('foo:bar'), \stdClass::class));

        $this->assertEquals(
            [new Relation('abcdef')],
            $providerProvider->getRelations(new RelationProvider(self::class . '::abc'), \stdClass::class)
        );
    }

    public static function abc(): array
    {
        return [new Relation('abcdef')];
    }
}
