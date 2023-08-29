<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\ClassMetadata;

use ReflectionClass;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Zuruuh\Hateoas\Mapping\Relation;

final class HateoasClassMetadata implements HateoasClassMetadataInterface
{
    /**
     * @var list<Relation>
     */
    private $relations = [];

    public function __construct(private readonly ClassMetadataInterface $decoratedClassMetadata) {}

    public function getName(): string
    {
        return $this->decoratedClassMetadata->getName();
    }

    public function addAttributeMetadata(AttributeMetadataInterface $attributeMetadata): void
    {
        $this->decoratedClassMetadata->addAttributeMetadata($attributeMetadata);
    }

    public function getAttributesMetadata(): array
    {
        return $this->decoratedClassMetadata->getAttributesMetadata();
    }

    public function merge(ClassMetadataInterface $classMetadata): void
    {
        $this->decoratedClassMetadata->merge($classMetadata);

        if ($classMetadata instanceof self) {
            foreach ($classMetadata->getRelations() as $relation) {
                $this->addRelation($relation);
            }
        }
    }

    /**
     * @return ReflectionClass<object>
     */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->decoratedClassMetadata->getReflectionClass();
    }

    public function getClassDiscriminatorMapping(): ?ClassDiscriminatorMapping
    {
        return $this->decoratedClassMetadata->getClassDiscriminatorMapping();
    }

    public function setClassDiscriminatorMapping(?ClassDiscriminatorMapping $mapping): void
    {
        $this->decoratedClassMetadata->setClassDiscriminatorMapping($mapping);
    }

    public function getRelations(): array
    {
        return $this->relations;
    }

    public function addRelation(Relation $relation): void
    {
        $this->relations[] = $relation;
    }
}
