<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Expression;

use RuntimeException;
use Symfony\Component\ExpressionLanguage\Expression;

interface ExpressionEvaluatorInterface
{
    /**
     * Evaluates an expression only if it matches "expr(...)".
     *
     * @param array<string, mixed> $names
     */
    public function evaluateExpressionIfNeeded(mixed $expression, array $names = []): mixed;

    /**
     * @param array<string, string|Expression> $expressions
     * @param array<string, mixed>             $names
     *
     * @return array<string, mixed>
     */
    public function evaluateArrayOfExpressionsIfNeeded(array $expressions, array $names = []): array;

    public function tryToEvaluateExpression(mixed $expression, object $object): mixed;

    /**
     * @param non-empty-string $expression
     * @param non-empty-string $path
     *
     * @return non-empty-string
     *
     * @throws RuntimeException
     */
    public function tryToEvaluateExpressionIntoNonEmptyString(string $expression, object $object, string $path): string;

    /**
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
    public function tryToEvaluateExpressionArray(array $array, object $object): array;
}
