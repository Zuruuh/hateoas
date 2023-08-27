<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Serializer\Metadata;

use Zuruuh\Hateoas\Configuration\Exclusion;
use Zuruuh\Hateoas\Configuration\Relation;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;

class RelationPropertyMetadata extends VirtualPropertyMetadata
{
    final public const EXPRESSION_REGEX = '/expr\((?P<expression>.+)\)/';

    public function __construct(?Exclusion $exclusion = null, ?Relation $relation = null)
    {
        if ($relation instanceof \Zuruuh\Hateoas\Configuration\Relation) {
            $this->name = $relation->getName();
            $this->class = get_class($relation);

            if (null !== $relation->getEmbedded()) {
                $this->type = ['name' => 'Hateoas\Model\Embedded', 'params' => []];
            } elseif (null !== $relation->getHref()) {
                $this->type = ['name' => 'Hateoas\Model\Link', 'params' => []];
            }
        }

        if (!$exclusion instanceof \Zuruuh\Hateoas\Configuration\Exclusion) {
            return;
        }

        $this->groups = $exclusion->getGroups();
        $this->sinceVersion = $exclusion->getSinceVersion();
        $this->untilVersion = $exclusion->getUntilVersion();
        $this->maxDepth = $exclusion->getMaxDepth();

        if ($exclusion->getExcludeIf() instanceof Expression) {
            $this->excludeIf = $exclusion->getExcludeIf();
        } elseif (null !== $exclusion->getExcludeIf()) {
            $this->excludeIf = $exclusion->getExcludeIf();
        }
    }
}
