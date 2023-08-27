<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Serializer;

use Zuruuh\Hateoas\Configuration\Exclusion;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\SerializationContext;

class ExclusionManager
{
    public function __construct(private readonly \JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy $expressionExclusionStrategy)
    {
    }

    public function shouldSkipLink(object $object, Relation $relation, SerializationContext $context): bool
    {
        if ($this->shouldSkipRelation($relation, $context)) {
            return true;
        }

        return null === $relation->getHref();
    }

    public function shouldSkipEmbedded(object $object, Relation $relation, SerializationContext $context): bool
    {
        if (null === $relation->getEmbedded()) {
            return true;
        }

        if (null === $relation->getEmbedded()->getExclusion()) {
            return $this->shouldSkipRelation($relation, $context);
        }

        return $this->shouldSkip($relation, $context, $relation->getEmbedded()->getExclusion());
    }

    private function shouldSkipRelation(Relation $relation, SerializationContext $context): bool
    {
        return $this->shouldSkip($relation, $context, $relation->getExclusion());
    }

    private function shouldSkip(Relation $relation, SerializationContext $context, ?Exclusion $exclusion = null): bool
    {
        $propertyMetadata = new RelationPropertyMetadata($exclusion, $relation);
        if ($context->getExclusionStrategy() instanceof \JMS\Serializer\Exclusion\ExclusionStrategyInterface && $context->getExclusionStrategy()->shouldSkipProperty($propertyMetadata, $context)) {
            return true;
        }

        return $exclusion instanceof \Zuruuh\Hateoas\Configuration\Exclusion
            && null !== $exclusion->getExcludeIf()
            && $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $context);
    }
}
