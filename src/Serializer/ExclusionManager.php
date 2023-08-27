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
    /**
     * @var ExpressionLanguageExclusionStrategy
     */
    private $expressionExclusionStrategy;

    public function __construct(ExpressionLanguageExclusionStrategy $expressionLanguageExclusionStrategy)
    {
        $this->expressionExclusionStrategy = $expressionLanguageExclusionStrategy;
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
        if ($context->getExclusionStrategy()) {
            if ($context->getExclusionStrategy()->shouldSkipProperty($propertyMetadata, $context)) {
                return true;
            }
        }

        return null !== $exclusion
            && null !== $exclusion->getExcludeIf()
            && $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $context);
    }
}
