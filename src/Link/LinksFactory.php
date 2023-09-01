<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Link;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassResolverTrait;
use Zuruuh\Hateoas\Exclusion\ExclusionManagerInterface;

final class LinksFactory implements LinksFactoryInterface
{
    use ClassResolverTrait;

    public function __construct(
        private readonly ClassMetadataFactoryInterface $classMetadataFactory,
        private readonly LinkFactoryInterface $linkFactory,
        private readonly ExclusionManagerInterface $exclusionManager,
    ) {}

    public function create(object $object): array
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getMetadataFor($class);
        $links = [];

        $relations = $classMetadata->getAttributesMetadata()['_links'] ?? null;

        foreach ($classMetadata->getAttributesMetadata() as $relation) {
            if ($this->exclusionManager->shouldSkipLink($object, $relation)) {
                continue;
            }

            $links[] = $this->linkFactory->createLink($object, $relation);
        }

        return $links;
    }
}
