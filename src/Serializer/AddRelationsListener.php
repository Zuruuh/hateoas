<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Serializer;

use JMS\Serializer\EventDispatcher\ObjectEvent;
use Zuruuh\Hateoas\Factory\EmbeddedsFactory;
use Zuruuh\Hateoas\Factory\LinksFactory;
use Zuruuh\Hateoas\Serializer\Metadata\InlineDeferrer;

class AddRelationsListener
{
    public function __construct(private readonly SerializerInterface $serializer, private readonly LinksFactory $linksFactory, private readonly EmbeddedsFactory $embeddedsFactory, private readonly InlineDeferrer $embeddedsInlineDeferrer, private readonly InlineDeferrer $linksInlineDeferrer) {}

    public function onPostSerialize(ObjectEvent $event): void
    {
        $object = $event->getObject();
        $context = $event->getContext();

        $context->startVisiting($object);

        $embeddeds = $this->embeddedsFactory->create($object, $context);
        $links = $this->linksFactory->create($object, $context);

        $embeddeds = $this->embeddedsInlineDeferrer->handleItems($object, $embeddeds, $context);
        $links = $this->linksInlineDeferrer->handleItems($object, $links, $context);

        if ([] !== $links) {
            $this->serializer->serializeLinks($links, $event->getVisitor(), $context);
        }

        if ([] !== $embeddeds) {
            $this->serializer->serializeEmbeddeds($embeddeds, $event->getVisitor(), $context);
        }

        $context->stopVisiting($object);
    }
}
