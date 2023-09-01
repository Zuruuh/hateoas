<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Relation;

use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Zuruuh\Hateoas\Mapping\Relation;

/**
 * @internal
 */
final class RelationMetadata implements AttributeMetadataInterface
{
    /**
     * @param list<Relation> $relations
     */
    public function __construct(
        private readonly AttributeMetadataInterface $inner,
        public readonly array $relations
    ) {}

    public function __set(string $name, mixed $value): void
    {
        $this->inner->{$name} = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->inner->{$name};
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->inner->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function addGroup(string $group): void
    {
        $this->inner->addGroup($group);
    }

    /**
     * {@inheritDoc}
     */
    public function getGroups(): array
    {
        return $this->inner->getGroups();
    }

    /**
     * {@inheritDoc}
     */
    public function setMaxDepth(?int $maxDepth): void
    {
        $this->inner->setMaxDepth($maxDepth);
    }

    /**
     * {@inheritDoc}
     */
    public function getMaxDepth(): ?int
    {
        return $this->inner->getMaxDepth();
    }

    /**
     * {@inheritDoc}
     */
    public function setSerializedName(?string $serializedName): void
    {
        $this->inner->setSerializedName($serializedName);
    }

    /**
     * {@inheritDoc}
     */
    public function getSerializedName(): ?string
    {
        return $this->inner->getSerializedName();
    }

    /**
     * {@inheritDoc}
     */
    public function setSerializedPath(?PropertyPath $serializedPath): void
    {
        $this->inner->setSerializedPath($serializedPath);
    }

    /**
     * {@inheritDoc}
     */
    public function getSerializedPath(): ?PropertyPath
    {
        return $this->inner->getSerializedPath();
    }

    /**
     * {@inheritDoc}
     */
    public function setIgnore(bool $ignore): void
    {
        $this->inner->setIgnore($ignore);
    }

    /**
     * {@inheritDoc}
     */
    public function isIgnored(): bool
    {
        return $this->inner->isIgnored();
    }

    /**
     * {@inheritDoc}
     */
    public function merge(AttributeMetadataInterface $attributeMetadata): void
    {
        $this->inner->merge($attributeMetadata);
    }

    /**
     * {@inheritDoc}
     */
    public function getNormalizationContexts(): array
    {
        return $this->inner->getNormalizationContexts();
    }

    /**
     * {@inheritDoc}
     */
    public function getNormalizationContextForGroups(array $groups): array
    {
        return $this->inner->getNormalizationContextForGroups($groups);
    }

    /**
     * {@inheritDoc}
     */
    public function setNormalizationContextForGroups(array $context, array $groups = []): void
    {
        $this->inner->setNormalizationContextForGroups($context, $groups);
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationContexts(): array
    {
        return $this->inner->getDenormalizationContexts();
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationContextForGroups(array $groups): array
    {
        return $this->inner->getDenormalizationContextForGroups($groups);
    }

    /**
     * {@inheritDoc}
     */
    public function setDenormalizationContextForGroups(array $context, array $groups = []): void
    {
        $this->inner->setDenormalizationContextForGroups($context, $groups);
    }
}
