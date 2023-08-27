<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Serializer;

use Zuruuh\Hateoas\Configuration\Exclusion;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Serializer\Metadata\RelationPropertyMetadata;

class ExclusionManager
{
    public function shouldSkipLink(object $object, Relation $relation): bool
    {
        if ($this->shouldSkipRelation($relation, $context)) {
            return true;
        }

        return null === $relation->href;
    }

    public function shouldSkipEmbedded(object $object, Relation $relation, SerializationContext $context): bool
    {
        if (null === $relation->embedded) {
            return true;
        }

        if (null === $relation->embedded->exclusion) {
            return $this->shouldSkipRelation($relation, $context);
        }

        return $this->shouldSkip($relation, $context, $relation->embedded->exclusion);
    }

    private function shouldSkipRelation(Relation $relation, SerializationContext $context): bool
    {
        return $this->shouldSkip($relation, $context, $relation->exclusion);
    }

    private function shouldSkip(Relation $relation, SerializationContext $context, ?Exclusion $exclusion = null): bool
    {
        $propertyMetadata = new RelationPropertyMetadata($exclusion, $relation);
        if ($context->exclusionStrategy instanceof \JMS\Serializer\Exclusion\ExclusionStrategyInterface && $context->exclusionStrategy->shouldSkipProperty($propertyMetadata, $context)) {
            return true;
        }

        return $exclusion instanceof \Zuruuh\Hateoas\Configuration\Exclusion
            && null !== $exclusion->excludeIf
            && $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $context);
    }
}
