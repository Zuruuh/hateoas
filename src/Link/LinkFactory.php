<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Link;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Mapping\Route;
use Zuruuh\Hateoas\Model\Link;
use Zuruuh\Hateoas\UrlGenerator\Registry\UrlGeneratorRegistryInterface;

final class LinkFactory implements LinkFactoryInterface
{
    public function __construct(
        private readonly UrlGeneratorRegistryInterface $urlGeneratorRegistry,
        private readonly ExpressionLanguage $expressionLanguage,
    ) {}

    public function createLink(object $object, Relation $relation): Link
    {
        $data = ['object' => $object];

        $rel = $relation->name;
        $href = $relation->href;

        if ($href instanceof Route) {
            if (!$this->urlGeneratorRegistry->hasGenerators()) {
                throw new \RuntimeException('You cannot use a route without an url generator.');
            }

            $name = (string) $this->tryToEvaluateExpression($href->name, $data);

            $parameters = is_array($href->parameters)
                ? $this->tryToEvaluateExpressions($href->parameters, $data)
                : $this->tryToEvaluateExpression($href->parameters, $data);

            $isAbsolute = (bool) $this->tryToEvaluateExpression($href->absolute, $data);

            if (!is_array($parameters)) {
                throw new \RuntimeException(
                    sprintf(
                        'The route parameters should be an array, %s given. Maybe you forgot to wrap the expression in expr(...).',
                        gettype($parameters)
                    )
                );
            }

            $href = $this->urlGeneratorRegistry
                ->get($href->generator)
                ->generate($name, $parameters, $isAbsolute);
        } else {
            $href = (string) $this->tryToEvaluateExpression($href, $data);
        }

        $attributes = $this->tryToEvaluateExpressions($relation->attributes, $data);

        return new Link($rel, $href, $attributes);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function tryToEvaluateExpression(mixed $exp, array $data): mixed
    {
        if ($exp instanceof Expression || is_string($exp)) {
            return $this->expressionLanguage->evaluate($exp, $data);
        }

        return $exp;
    }

    /**
     * @param array<array-key, mixed> $array
     * @param array<string, mixed> $data
     *
     * @return array<array-key, mixed>
     */
    private function tryToEvaluateExpressions(array $array, array $data): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $value = is_array($value)
                ? $this->tryToEvaluateExpressions($value, $data)
                : $this->tryToEvaluateExpression($value, $data);

            $newArray[$key] = $value;
        }

        return $newArray;
    }
}
