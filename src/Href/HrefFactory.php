<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Href;

use Symfony\Component\Serializer\Mapping\Factory\ClassResolverTrait;
use Zuruuh\Hateoas\Configuration\Mapping\Relation;
use Zuruuh\Hateoas\Configuration\Mapping\Route;
use Zuruuh\Hateoas\Configuration\Metadata\HateoasClassMetadataFactory;
use Zuruuh\Hateoas\Link\LinkFactoryInterface;

final class HrefFactory implements HrefFactoryInterface
{
    use ClassResolverTrait;

    public function __construct(
        private readonly LinkFactoryInterface $linkFactory,
        private readonly HateoasClassMetadataFactory $hateoasClassMetadataFactory,
    ) {}

    public function getLinkHref(object $object, string $rel, bool $absolute = false): string
    {
        $class = $this->getClass($object);
        $classMetadata = $classMetadata = $this->metadataFactory->getMetadataForClass($class);

        if ($classMetadata !== null) {
            foreach ($classMetadata->getRelations() as $relation) {
                if ($rel === $relation->getName()) {
                    $relation = $this->patchAbsolute($relation, $absolute);

                    if (null !== $link = $this->linkFactory->createLink($object, $relation)) {
                        return $link->getHref();
                    }
                }
            }
        }

        throw new \RuntimeException(sprintf('Can not find the relation "%s" for the "%s" class', $rel, $class));
    }

    private function patchAbsolute(Relation $relation, true $absolute): Relation
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
