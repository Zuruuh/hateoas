<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Provider;

use stdClass;
use Zuruuh\Hateoas\Configuration\Provider\FunctionProvider;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\RelationProvider;
use Zuruuh\Hateoas\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MethodProviderTest extends TestCase
{
    public function test(): void
    {
        $providerProvider = new FunctionProvider();

        $this->assertEquals(
            [new Relation('abcde')],
            $providerProvider->getRelations(new RelationProvider('func(Hateoas\Tests\Configuration\Provider\abc)'), stdClass::class)
        );
        $this->assertEquals(
            [new Relation('abcdef')],
            $providerProvider->getRelations(new RelationProvider('func('.self::class.'::abc)'), stdClass::class)
        );
    }

    public static function abc(): array
    {
        return [new Relation('abcdef')];
    }
}

function abc()
{
    return [new Relation('abcde')];
}
