<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Helper;

use RuntimeException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Zuruuh\Hateoas\Configuration\Metadata\ClassAndRelationsMetadataFactoryInterface;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Factory\LinkFactory;
use Zuruuh\Hateoas\Model\Link;

class LinkHelper
{
    public function __construct(
        private readonly LinkFactory $linkFactory,
        private readonly ClassAndRelationsMetadataFactoryInterface $metadataFactory
    ) {}

    public function getLinkHref(object $object, string $rel, bool $absolute = false): string
    {
        $classMetadata = $this->metadataFactory->getMetadataFor($object::class);

        if ($classMetadata !== null) {
            foreach ($classMetadata->getRelations() as $relation) {
                if ($rel === $relation->name) {
                    $relation = $this->patchAbsolute($relation, $absolute);
                    $link = $this->linkFactory->createLink($object, $relation);

                    if ($link instanceof Link) {
                        return $link->href;
                    }
                }
            }
        }

        throw new RuntimeException(sprintf('Can not find the relation "%s" for the "%s" class', $rel, $object::class));
    }

    private function patchAbsolute(Relation $relation, bool $absolute): Relation
    {
        $href = $relation->href;

        if ($href instanceof Route) {
            $href = new Route(
                $href->name,
                $href->parameters,
                $absolute,
                $href->generator,
            );
        }

        return new Relation(
            $relation->name,
            $href,
            $relation->embedded,
            $relation->attributes,
            $relation->exclusion,
        );
    }
}
