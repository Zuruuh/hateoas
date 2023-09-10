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

    /**
     * @api
     */
    public function __set(string $name, mixed $value): void
    {
        $this->inner->{$name} = $value;
    }

    /**
     * @api
     */
    public function __get(string $name): mixed
    {
        return $this->inner->{$name};
    }

    public function getName(): string
    {
        return $this->inner->getName();
    }

    public function addGroup(string $group): void
    {
        $this->inner->addGroup($group);
    }

    /**
     * @return list<string>
     */
    public function getGroups(): array
    {
        return $this->inner->getGroups();
    }

    public function setMaxDepth(?int $maxDepth): void
    {
        $this->inner->setMaxDepth($maxDepth);
    }

    public function getMaxDepth(): ?int
    {
        return $this->inner->getMaxDepth();
    }

    public function setSerializedName(?string $serializedName = null): void
    {
        $this->inner->setSerializedName($serializedName);
    }

    public function getSerializedName(): ?string
    {
        return $this->inner->getSerializedName();
    }

    public function setSerializedPath(?PropertyPath $serializedPath): void
    {
        $this->inner->setSerializedPath($serializedPath);
    }

    public function getSerializedPath(): ?PropertyPath
    {
        return $this->inner->getSerializedPath();
    }

    public function setIgnore(bool $ignore): void
    {
        $this->inner->setIgnore($ignore);
    }

    public function isIgnored(): bool
    {
        return $this->inner->isIgnored();
    }

    public function merge(AttributeMetadataInterface $attributeMetadata): void
    {
        $this->inner->merge($attributeMetadata);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getNormalizationContexts(): array
    {
        return $this->inner->getNormalizationContexts();
    }

    /**
     * @param array<array-key, mixed> $groups
     *
     * @return array<array-key, mixed>
     */
    public function getNormalizationContextForGroups(array $groups): array
    {
        return $this->inner->getNormalizationContextForGroups($groups);
    }

    /**
     * @param array<array-key, mixed> $context
     * @param array<array-key, mixed> $groups
     */
    public function setNormalizationContextForGroups(array $context, array $groups = []): void
    {
        $this->inner->setNormalizationContextForGroups($context, $groups);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getDenormalizationContexts(): array
    {
        return $this->inner->getDenormalizationContexts();
    }

    /**
     * @param array<array-key, mixed> $groups
     *
     * @return array<array-key, mixed>
     */
    public function getDenormalizationContextForGroups(array $groups): array
    {
        return $this->inner->getDenormalizationContextForGroups($groups);
    }

    /**
     * @param array<array-key, mixed> $context
     * @param array<array-key, mixed> $groups
     *
     * @return array<array-key, mixed>
     */
    public function setDenormalizationContextForGroups(array $context, array $groups = []): void
    {
        $this->inner->setDenormalizationContextForGroups($context, $groups);
    }
}
