<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Expression;

use Error;
use RuntimeException;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ExpressionEvaluator implements ExpressionEvaluatorInterface
{
    public function __construct(private readonly ExpressionLanguage $expressionLanguage) {}

    public function evaluateExpressionIfNeeded(mixed $expression, array $names = []): mixed
    {
        $names[] = 'object';

        if (is_string($expression) && preg_match('/expr\((?P<expression>.+)\)/', $expression, $matches)) {
            return $this->expressionLanguage->parse($matches['expression'], $names);
        }
        if ($expression instanceof Expression) {
            return $this->expressionLanguage->parse($expression, $names);
        }

        return $expression;
    }

    public function evaluateArrayOfExpressionsIfNeeded(array $expressions, array $names = []): array
    {
        $evaluatedExpressions = [];

        foreach ($expressions as $name => $expression) {
            $evaluatedExpressions[$name] = $this->evaluateExpressionIfNeeded($expression, $names);
        }

        return $evaluatedExpressions;
    }

    public function tryToEvaluateExpression(mixed $expression, object $object): mixed
    {
        if ($expression instanceof Expression || is_string($expression)) {
            return $this->expressionLanguage->evaluate($expression, ['object' => $object]);
        }

        return $expression;
    }

    public function tryToEvaluateExpressionIntoNonEmptyString(string $expression, object $object, string $path): string
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

    public function tryToEvaluateExpressionArray(array $array, object $object): array
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
