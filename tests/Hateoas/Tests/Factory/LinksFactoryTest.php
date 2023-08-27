<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Factory;

use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;
use Zuruuh\Hateoas\Configuration\Metadata\ClassMetadata;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Factory\LinksFactory;
use Zuruuh\Hateoas\Model\Link;
use Zuruuh\Hateoas\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class LinksFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function test(): void
    {
        $object = new stdClass();
        $context = SerializationContext::create();

        $relations = [
            new Relation('self', '/users/1'),
            new Relation('manager', '/users/2'),
        ];
        $link = new Link('', '');

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata
            ->getRelations()
            ->willReturn($relations)
            ->shouldBeCalledTimes(1)
        ;

        $metadataFactory = $this->prophesize(MetadataFactoryInterface::class);
        $metadataFactory
            ->getMetadataForClass($object::class)
            ->willReturn($metadata)
            ->shouldBeCalledTimes(1)
        ;
        $linkFactoryProphecy = $this->prophesize('Hateoas\Factory\LinkFactory');
        $linkFactoryProphecy
            ->createLink($object, $relations[1], $context)
            ->willReturn($link)
            ->shouldBeCalledTimes(1)
        ;
        $exclusionManagerProphecy = $this->prophesize('Hateoas\Serializer\ExclusionManager');
        $exclusionManagerProphecy
            ->shouldSkipLink($object, $relations[0], $context)
            ->willReturn(true)
            ->shouldBeCalledTimes(1)
        ;
        $exclusionManagerProphecy
            ->shouldSkipLink($object, $relations[1], $context)
            ->willReturn(false)
            ->shouldBeCalledTimes(1)
        ;

        $linksFactory = new LinksFactory(
            $metadataFactory->reveal(),
            $linkFactoryProphecy->reveal(),
            $exclusionManagerProphecy->reveal()
        );

        $links = $linksFactory->create($object, $context);

        $this->assertCount(1, $links);
        $this->assertContains($link, $links);
    }
}
