<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Serializer;

use Zuruuh\Hateoas\Model\Embedded;
use Zuruuh\Hateoas\Model\Link;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

interface SerializerInterface
{
    /**
     * @param Link[]                   $links
     */
    public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context): void;

    /**
     * @param Embedded[]               $embeddeds
     */
    public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context): void;
}
