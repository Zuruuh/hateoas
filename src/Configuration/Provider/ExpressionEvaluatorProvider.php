<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Provider;

use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use Zuruuh\Hateoas\Configuration\RelationProvider;

class ExpressionEvaluatorProvider implements RelationProviderInterface
{
    public function __construct(private readonly \JMS\Serializer\Expression\ExpressionEvaluatorInterface $expressionEvaluator) {}

    /**
     * {@inheritdoc}
     */
    public function getRelations(RelationProvider $configuration, string $class): array
    {
        if (!preg_match('/expr\((?P<expression>.+)\)/', (string) $configuration->getName(), $matches)) {
            return [];
        }

        return $this->expressionEvaluator->evaluate($matches['expression'], ['class' => $class]);
    }
}
