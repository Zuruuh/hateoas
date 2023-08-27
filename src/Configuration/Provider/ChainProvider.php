<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Provider;

use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\RelationProvider;

final class ChainProvider implements RelationProviderInterface
{
    /**
     * @param list<RelationProviderInterface> $resolvers
     */
    public function __construct(private array $resolvers) {}

    public function addProvider(RelationProviderInterface $resolver): void
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * @return list<Relation>
     */
    public function getRelations(RelationProvider $configuration, string $class): array
    {
        $relations = [];
        foreach ($this->resolvers as $resolver) {
            $relations = array_merge($relations, $resolver->getRelations($configuration, $class));
        }

        return $relations;
    }
}
