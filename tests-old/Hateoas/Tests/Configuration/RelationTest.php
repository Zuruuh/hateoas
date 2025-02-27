<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration;

use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RelationTest extends TestCase
{
    public function testConstructor(): void
    {
        $relation = new Relation('self', 'user_get');

        $this->assertSame('self', $relation->getName());
        $this->assertSame('user_get', $relation->getHref());
        $this->assertEmpty($relation->getAttributes());
    }

    public function requireHrefOrEmbed(): void
    {
        $this
            ->exception(function (): void {
                new Relation('', null, null);
            })
            ->isInstanceOf('InvalidArgumentException')
            ->hasMessage('$href and $embedded cannot be both null.')
        ;
    }

    public function canBeConstructedWithOnlyAnEmbed(): void
    {
        $relation = new Relation('self', null, 'foo');

        $this->assertSame('self', $relation->getName());
        $this->assertNull($relation->getHref());
        $this->assertEmpty($relation->getAttributes());
        $this->assertInstanceOf('Hateoas\Configuration\Embed', $relation->getEmbedded());
        $this->assertSame('foo', $relation->getEmbedded()->getContent());
    }
}
