<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Link;

use Error;
use RuntimeException;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Zuruuh\Hateoas\Mapping\Relation;
use Zuruuh\Hateoas\Mapping\Route;
use Zuruuh\Hateoas\Model\Link;
use Zuruuh\Hateoas\UrlGenerator\Registry\UrlGeneratorRegistryInterface;

/**
 * @immutable
 */
final class LinkFactory implements LinkFactoryInterface
{
    public function __construct(
        private readonly UrlGeneratorRegistryInterface $urlGeneratorRegistry,
        private readonly ExpressionLanguage $expressionLanguage,
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

            $name = $this->tryToEvaluateExpressionIntoNonEmptyString($href->name, $object, "{$relation->name}:{$href->name}");

            /**
             * @var array<string, mixed> $parameters
             */
            $parameters = is_array($href->parameters)
                ? $this->tryToEvaluateExpressionArray($href->parameters, $object)
                : $this->tryToEvaluateExpression($href->parameters, $object);

            $isAbsolute = (bool) $this->tryToEvaluateExpression($href->absolute, $object);

            if (!is_array($parameters)) {
                throw new RuntimeException(sprintf('The route parameters should be an array, %s given. Maybe you forgot to wrap the expression in expr(...).', gettype($parameters)));
            }

            $href = $this->urlGeneratorRegistry
                ->get($href->generator)
                ->generate($name, $parameters, $isAbsolute)
            ;
        } else {
            $href = $this->tryToEvaluateExpressionIntoNonEmptyString($href, $object, "{$relation->name}:{$href}");
        }

        $attributes = $this->tryToEvaluateExpressionArray($relation->attributes, $object);

        return new Link($rel, $href, $attributes);
    }

    private function tryToEvaluateExpression(mixed $expression, object $object): mixed
    {
        if ($expression instanceof Expression || is_string($expression)) {
            return $this->expressionLanguage->evaluate($expression, ['object' => $object]);
        }

        return $expression;
    }

    /**
     * @param non-empty-string $expression
     * @param non-empty-string $path
     *
     * @return non-empty-string
     *
     * @throws RuntimeException
     */
    private function tryToEvaluateExpressionIntoNonEmptyString(string $expression, object $object, string $path): string
    {
        try {
            /**
             * @phpstan-ignore-next-line
             */
            $value = (string) $this->tryToEvaluateExpression($expression, $object);
            if ($value === '' || trim($value) === '') {
                throw new RuntimeException("Could not generate a value for {$path}!");
            }

            return $value;
        } catch (Error $e) {
            throw new RuntimeException("Could not generate a value for for {$path}!", previous: $e);
        }
    }

    /**
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
    private function tryToEvaluateExpressionArray(array $array, object $object): array
    {
        $newArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                /**
                 * @var array<string, mixed> $innerArray
                 */
                $innerArray = $value;
                $newArray[$key] = $this->tryToEvaluateExpressionArray($innerArray, $object);

                continue;
            }

            $newArray[$key] = $this->tryToEvaluateExpression($value, $object);
        }

        return $newArray;
    }
}
