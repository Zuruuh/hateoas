<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Provider;

use Zuruuh\Hateoas\Configuration\RelationProvider;

class ChainProvider implements RelationProviderInterface
{
    /**
     * @param \Zuruuh\Hateoas\Configuration\Provider\RelationProviderInterface[] $resolvers
     */
    public function __construct(private array $resolvers)
    {
    }

    public function addProvider(RelationProviderInterface $resolver): void
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * {@inheritdoc}
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
