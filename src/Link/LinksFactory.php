<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Link;

use Symfony\Component\Serializer\Mapping\Factory\ClassResolverTrait;
use Zuruuh\Hateoas\ClassMetadata\Factory\HateoasClassMetadataFactoryInterface;
use Zuruuh\Hateoas\Exclusion\ExclusionManagerInterface;

final class LinksFactory implements LinksFactoryInterface
{
    use ClassResolverTrait;

    public function __construct(
        private readonly HateoasClassMetadataFactoryInterface $classMetadataFactory,
        private readonly LinkFactoryInterface $linkFactory,
        private readonly ExclusionManagerInterface $exclusionManager,
    ) {}

    public function create(object $object): array
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getMetadataFor($class);
        $links = [];

        foreach ($classMetadata->getRelations() as $relation) {
            if ($this->exclusionManager->shouldSkipLink($object, $relation)) {
                continue;
            }

            $links[] = $this->linkFactory->createLink($object, $relation);
        }

        return $links;
    }
}
