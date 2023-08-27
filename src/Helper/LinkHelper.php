<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Helper;

use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Factory\LinkFactory;
use Zuruuh\Hateoas\Util\ClassUtils;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;

class LinkHelper
{
    public function __construct(private readonly \Zuruuh\Hateoas\Factory\LinkFactory $linkFactory, private readonly \Metadata\MetadataFactoryInterface $metadataFactory)
    {
    }

    public function getLinkHref(object $object, string $rel, bool $absolute = false, ?SerializationContext $context = null): string
    {
        $context = $context ?? SerializationContext::create();

        if (null !== ($classMetadata = $this->metadataFactory->getMetadataForClass(ClassUtils::getClass($object)))) {
            foreach ($classMetadata->getRelations() as $relation) {
                if ($rel === $relation->getName()) {
                    $relation = $this->patchAbsolute($relation, $absolute);

                    if (($link = $this->linkFactory->createLink($object, $relation, $context)) instanceof \Zuruuh\Hateoas\Model\Link) {
                        return $link->getHref();
                    }
                }
            }
        }

        throw new \RuntimeException(sprintf('Can not find the relation "%s" for the "%s" class', $rel, ClassUtils::getClass($object)));
    }

    /**
     * @param mixed $absolute
     */
    private function patchAbsolute(Relation $relation, bool $absolute): Relation
    {
        $href = $relation->getHref();

        if ($href instanceof Route) {
            $href = new Route(
                $href->getName(),
                $href->getParameters(),
                $absolute,
                $href->getGenerator()
            );
        }

        return new Relation(
            $relation->getName(),
            $href,
            $relation->getEmbedded(),
            $relation->getAttributes(),
            $relation->getExclusion()
        );
    }
}
