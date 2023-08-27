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
    public function __construct(
        public readonly ClassMetadataInterface $decoratedClassMetadata
    ) {}

    /**
     * @var list<Relation> $relations
     */
    private array $relations;

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

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->decoratedClassMetadata->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function addAttributeMetadata(AttributeMetadataInterface $attributeMetadata): void
    {
        $this->decoratedClassMetadata->addAttributeMetadata($attributeMetadata);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributesMetadata(): array
    {
        return $this->decoratedClassMetadata->getAttributesMetadata();
    }

    /**
     * {@inheritDoc}
     */
    public function merge(self $classMetadata): void
    {
        $this->decoratedClassMetadata->merge($classMetadata);

        if ($classMetadata instanceof static) {
            $this->relations = array_unique([...$this->relations, ...$classMetadata->getRelations()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->decoratedClassMetadata->getReflectionClass();
    }

    /**
     * {@inheritDoc}
     */
    public function getClassDiscriminatorMapping(): ?ClassDiscriminatorMapping
    {
        return $this->decoratedClassMetadata->getClassDiscriminatorMapping();
    }

    /**
     * {@inheritDoc}
     */
    public function setClassDiscriminatorMapping(?ClassDiscriminatorMapping $mapping): void
    {
        $this->decoratedClassMetadata->setClassDiscriminatorMapping($mapping);
    }
}
