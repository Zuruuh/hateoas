<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Href;

use RuntimeException;
use Symfony\Component\Serializer\Mapping\Factory\ClassResolverTrait;
use Zuruuh\Hateoas\ClassMetadata\Factory\HateoasClassMetadataFactoryInterface;
use Zuruuh\Hateoas\Link\LinkFactoryInterface;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Mapping\Route;

final class HrefFactory implements HrefFactoryInterface
{
    use ClassResolverTrait;

    public function __construct(
        private readonly LinkFactoryInterface $linkFactory,
        private readonly HateoasClassMetadataFactoryInterface $classMetadataFactory,
    ) {}

    public function getLinkHref(object $object, string $rel, bool $absolute = false): string
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getMetadataFor($class);

        foreach ($classMetadata->getRelations() as $relation) {
            if ($rel === $relation->name) {
                $relation = $this->patchAbsolute($relation, $absolute);
                $link = $this->linkFactory->createLink($object, $relation);

                return $link->href;
            }
        }

        throw new RuntimeException(sprintf('Can not find the relation "%s" for the "%s" class', $rel, $class));
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
