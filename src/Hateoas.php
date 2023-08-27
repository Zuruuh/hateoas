<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas;

use Zuruuh\Hateoas\Helper\LinkHelper;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class Hateoas implements SerializerInterface
{
    public function __construct(private readonly \JMS\Serializer\SerializerInterface $serializer, private readonly \Zuruuh\Hateoas\Helper\LinkHelper $linkHelper)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, string $format, ?SerializationContext $context = null, ?string $type = null): string
    {
        return $this->serializer->serialize($data, $format, $context, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize(string $data, string $type, string $format, ?DeserializationContext $context = null)
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    public function getLinkHelper(): LinkHelper
    {
        return $this->linkHelper;
    }
}
