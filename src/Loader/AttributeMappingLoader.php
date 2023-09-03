<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Loader;

use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Mapping\RelationProvider;
use Zuruuh\Hateoas\Relation\RelationMetadata;

/**
 * @api
 */
final class AttributeMappingLoader implements LoaderInterface
{
    /**
     * @internal
     */
    public const INTERNAL_RELATION_METADATA_KEY = '_relations';

    public function __construct(
        private readonly LoaderInterface $decoratedLoader,
    ) {}

    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        $relations = [];
        $this->decoratedLoader->loadClassMetadata($classMetadata);

        $classAttributes = $classMetadata->getReflectionClass()->getAttributes();

        foreach ($classAttributes as $classAttribute) {
            if (is_subclass_of($classAttribute->getName(), Relation::class)) {
                $relation = $classAttribute->newInstance();
                assert($relation instanceof Relation);

                $relations[] = $relation;
            }
        }

        $reflectionMethods = $classMetadata->getReflectionClass()->getMethods();

        foreach ($reflectionMethods as $reflectionMethod) {
            foreach ($reflectionMethod->getAttributes() as $attribute) {
                $attributeName = $attribute->getName();

                if (is_subclass_of($attributeName, Relation::class)) {
                    $relation = $attribute->newInstance();
                    assert($relation instanceof Relation);

                    $relations[] = $relation;

                    continue;
                }

                if (is_subclass_of($attributeName, RelationProvider::class) || $attributeName === RelationProvider::class) {
                    $providedRelations = $reflectionMethod->invoke(null);
                    assert(is_array($providedRelations));

                    foreach ($providedRelations as $relation) {
                        assert($relation instanceof Relation);
                        $relations[] = $relation;
                    }
                }
            }
        }

        $attributeMetadata = new RelationMetadata(new AttributeMetadata(self::INTERNAL_RELATION_METADATA_KEY), $relations);
        $classMetadata->addAttributeMetadata($attributeMetadata);

        return $relations !== [];
    }
}
