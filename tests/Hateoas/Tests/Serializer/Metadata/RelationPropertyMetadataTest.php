<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Serializer\Metadata;

use Zuruuh\Hateoas\Configuration\Embedded;
use Zuruuh\Hateoas\Configuration\Exclusion;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use Zuruuh\Hateoas\Tests\TestCase;

class RelationPropertyMetadataTest extends TestCase
{
    public function test()
    {
        $propertyMetadata = new RelationPropertyMetadata();

        $this->assertNull($propertyMetadata->groups);
        $this->assertNull($propertyMetadata->sinceVersion);
        $this->assertNull($propertyMetadata->untilVersion);
        $this->assertNull($propertyMetadata->maxDepth);
    }

    public function testWithExclusion()
    {
        $propertyMetadata = new RelationPropertyMetadata(new Exclusion(
            ['foo', 'bar'],
            '1.1',
            '2.2',
            42
        ));

        $this->assertSame(['foo', 'bar'], $propertyMetadata->groups);
        $this->assertSame('1.1', $propertyMetadata->sinceVersion);
        $this->assertSame('2.2', $propertyMetadata->untilVersion);
        $this->assertSame(42, $propertyMetadata->maxDepth);
    }

    public function testWithEmbeddedRelation()
    {
        $propertyMetadata = new RelationPropertyMetadata(null, new Relation(
            'foo',
            null,
            new Embedded('bar', ['name' => 'John'])
        ));

        $this->assertSame('foo', $propertyMetadata->name);
        $this->assertSame('Hateoas\Configuration\Relation', $propertyMetadata->class);
        $this->assertSame(
            [
                'name' => 'Hateoas\Model\Embedded',
                'params' => [],
            ],
            $propertyMetadata->type
        );
    }

    public function testWithLinkRelation()
    {
        $propertyMetadata = new RelationPropertyMetadata(null, new Relation(
            'foo',
            new Route('/route', ['foo' => 'bar'])
        ));

        $this->assertSame('foo', $propertyMetadata->name);
        $this->assertSame('Hateoas\Configuration\Relation', $propertyMetadata->class);
        $this->assertSame(
            [
                'name' => 'Hateoas\Model\Link',
                'params' => [],
            ],
            $propertyMetadata->type
        );
    }
}
