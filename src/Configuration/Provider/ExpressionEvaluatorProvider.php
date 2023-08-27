<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Provider;

use Zuruuh\Hateoas\Configuration\RelationProvider;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;

class ExpressionEvaluatorProvider implements RelationProviderInterface
{
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    public function __construct(ExpressionEvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelations(RelationProvider $configuration, string $class): array
    {
        if (!preg_match('/expr\((?P<expression>.+)\)/', $configuration->getName(), $matches)) {
            return [];
        }

        return $this->expressionEvaluator->evaluate($matches['expression'], ['class' => $class]);
    }
}
