<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Factory;

use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;
use Zuruuh\Hateoas\Model\Link;
use Zuruuh\Hateoas\Serializer\ExclusionManager;

class LinksFactory
{
    public function __construct(private readonly \Metadata\MetadataFactoryInterface $metadataFactory, private readonly \Zuruuh\Hateoas\Factory\LinkFactory $linkFactory, private readonly \Zuruuh\Hateoas\Serializer\ExclusionManager $exclusionManager) {}

    /**
     * @return Link[]
     */
    public function create(object $object, SerializationContext $context): array
    {
        $links = [];
        if (null !== ($classMetadata = $this->metadataFactory->getMetadataForClass($object::class))) {
            foreach ($classMetadata->getRelations() as $relation) {
                if ($this->exclusionManager->shouldSkipLink($object, $relation, $context)) {
                    continue;
                }

                $links[] = $this->linkFactory->createLink($object, $relation, $context);
            }
        }

        return $links;
    }
}
