<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata;

use ReflectionClass;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Zuruuh\Hateoas\Configuration\Relation;

final class ClassMetadataWithRelations implements ClassMetadataInterface
{
    /**
     * @var list<Relation>
     */
    private array $relations;

    public function __construct(
        public readonly ClassMetadataInterface $decoratedClassMetadata
    ) {}

    public function addRelation(Relation $relation): void
    {
        $this->relations[] = $relation;
    }

    /**
     * @return list<Relation>
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

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

    public function merge(self $classMetadata): void
    {
        $this->decoratedClassMetadata->merge($classMetadata);

        if ($classMetadata instanceof self) {
            $this->relations = array_unique([...$this->relations, ...$classMetadata->getRelations()]);
        }
    }

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
}
