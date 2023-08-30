<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Link;

use RuntimeException;
use Zuruuh\Hateoas\Expression\ExpressionEvaluatorInterface;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Mapping\Route;
use Zuruuh\Hateoas\Model\Link;
use Zuruuh\Hateoas\UrlGenerator\Registry\UrlGeneratorRegistryInterface;

final class LinkFactory implements LinkFactoryInterface
{
    public function __construct(
        private readonly UrlGeneratorRegistryInterface $urlGeneratorRegistry,
        private readonly ExpressionEvaluatorInterface $expressionEvaluator,
    ) {}

    public function createLink(object $object, Relation $relation): Link
    {
        $rel = $relation->name;
        $href = $relation->href;

        assert($href !== null);

        if ($href instanceof Route) {
            if (!$this->urlGeneratorRegistry->hasGenerators()) {
                throw new RuntimeException("You cannot use a route without an url generator at {$relation->name}:{$href->name}");
            }

            $name = $this->expressionEvaluator->tryToEvaluateExpressionIntoNonEmptyString($href->name, $object, "{$relation->name}:{$href->name}");

            /**
             * @var array<string, mixed> $parameters
             */
            $parameters = is_array($href->parameters)
                ? $this->expressionEvaluator->tryToEvaluateExpressionArray($href->parameters, $object)
                : $this->expressionEvaluator->tryToEvaluateExpression($href->parameters, $object);

            $isAbsolute = (bool) $this->expressionEvaluator->tryToEvaluateExpression($href->absolute, $object);

            if (!is_array($parameters)) {
                throw new RuntimeException(sprintf('The route parameters should be an array, %s given. Maybe you forgot to wrap the expression in expr(...).', gettype($parameters)));
            }

            $href = $this->urlGeneratorRegistry
                ->get($href->generator)
                ->generate($name, $parameters, $isAbsolute)
            ;
        } else {
            $href = $this->expressionEvaluator->tryToEvaluateExpressionIntoNonEmptyString($href, $object, "{$relation->name}:{$href}");
        }

        $attributes = $this->expressionEvaluator->tryToEvaluateExpressionArray($relation->attributes, $object);

        return new Link($rel, $href, $attributes);
    }
}
