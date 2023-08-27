<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Serializer;

use Zuruuh\Hateoas\Serializer\AddRelationsListener;
use Zuruuh\Hateoas\Tests\TestCase;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AddRelationsListenerTest extends TestCase
{
    use ProphecyTrait;

    protected function createEventSubscriber($serializer, $linksFactory, $embedsFactory): \Zuruuh\Hateoas\Serializer\AddRelationsListener
    {
        $inlineDeferrerProphecy = $this->prophesize('Hateoas\Serializer\Metadata\InlineDeferrer');
        $inlineDeferrerProphecy
            ->handleItems(Argument::cetera())
            ->will(function (array $args) {
                return $args[1];
            });

        return new AddRelationsListener(
            $serializer,
            $linksFactory,
            $embedsFactory,
            $inlineDeferrerProphecy->reveal(),
            $inlineDeferrerProphecy->reveal()
        );
    }

    protected function prophesizeSerializer(): \Prophecy\Prophecy\ObjectProphecy
    {
        return $this->prophesize('Hateoas\Serializer\SerializerInterface');
    }

    public function testOnPostSerialize(): void
    {
        $embeddeds = [
            $this->prophesize('Hateoas\Model\Embedded')->reveal(),
        ];
        $links = [
            $this->prophesize('Hateoas\Model\Link')->reveal(),
        ];
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $serializationVisitor = $this->mockSerializationVisitor();

        $serializerProphecy = $this->prophesizeSerializer();
        $serializerProphecy
            ->serializeEmbeddeds($embeddeds, $serializationVisitor, $context)
            ->shouldBeCalledTimes(1);
        $serializerProphecy
            ->serializeLinks($links, $serializationVisitor, $context)
            ->shouldBeCalledTimes(1);

        $linksFactoryProphecy = $this->prophesize('Hateoas\Factory\LinksFactory');
        $linksFactoryProphecy
            ->create($object, $context)
            ->willReturn($links)
            ->shouldBeCalledTimes(1);

        $embeddedsFactoryProphecy = $this->prophesize('Hateoas\Factory\EmbeddedsFactory');
        $embeddedsFactoryProphecy
            ->create($object, $context)
            ->willReturn($embeddeds)
            ->shouldBeCalledTimes(1);

        $eventProphecy = $this->mockEvent($object, $serializationVisitor, $context);

        $embeddedEventSubscriber = $this->createEventSubscriber(
            $serializerProphecy->reveal(),
            $linksFactoryProphecy->reveal(),
            $embeddedsFactoryProphecy->reveal()
        );
        $embeddedEventSubscriber->onPostSerialize($eventProphecy->reveal());
    }

    public function testOnPostSerializeWithNoLinksEmbeddeds(): void
    {
        $embeddeds = [];
        $links = [];
        $object = new \StdClass();
        $context = $this->prophesize('JMS\Serializer\SerializationContext')->reveal();

        $serializationVisitor = $this->mockSerializationVisitor();

        $serializerProphecy = $this->prophesizeSerializer();
        $serializerProphecy
            ->serializeEmbeddeds($embeddeds, $serializationVisitor, $context)
            ->shouldNotBeCalled();
        $serializerProphecy
            ->serializeLinks($links, $serializationVisitor)
            ->shouldNotBeCalled();

        $linksFactoryProphecy = $this->prophesize('Hateoas\Factory\LinksFactory');
        $linksFactoryProphecy
            ->create($object, $context)
            ->willReturn($links)
            ->shouldBeCalledTimes(1);

        $embeddedsFactoryProphecy = $this->prophesize('Hateoas\Factory\EmbeddedsFactory');
        $embeddedsFactoryProphecy
            ->create($object, $context)
            ->willReturn($embeddeds)
            ->shouldBeCalledTimes(1);

        $eventProphecy = $this->mockEvent($object, $serializationVisitor, $context);

        $embeddedEventSubscriber = $this->createEventSubscriber(
            $serializerProphecy->reveal(),
            $linksFactoryProphecy->reveal(),
            $embeddedsFactoryProphecy->reveal()
        );
        $embeddedEventSubscriber->onPostSerialize($eventProphecy->reveal());
    }

    private function mockSerializationVisitor()
    {
        return $this->prophesize(SerializationVisitorInterface::class)->reveal();
    }

    private function mockEvent(\StdClass $object, $serializationVisitor, $context)
    {
        $eventProphecy = $this->prophesize('JMS\Serializer\EventDispatcher\ObjectEvent');
        $eventProphecy->getObject()->willreturn($object);
        $eventProphecy->getVisitor()->willreturn($serializationVisitor);
        $eventProphecy->getContext()->willreturn($context);

        return $eventProphecy;
    }
}
