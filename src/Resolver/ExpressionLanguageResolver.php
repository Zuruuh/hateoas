<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Resolver;

use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;

final class ExpressionLanguageResolver
{
    private readonly CompilableExpressionEvaluatorInterface $expressionLanguage;

    /**
     * @param mixed $exp
     *
     * @return Expression|mixed
     */
    private function checkExpression($exp, array $names = [])
    {
        if (is_string($exp) && preg_match('/expr\((?P<expression>.+)\)/', $exp, $matches)) {
            $names = array_merge($names, ['object', 'context', 'metadata']);

            return $this->expressionLanguage->parse($matches['expression'], $names);
        }

        return $exp;
    }

    private function checkExpressionArray(array $data): array
    {
        $newArray = [];
        foreach ($data as $key => $value) {
            $newArray[$key] = $this->checkExpression($value);
        }

        return $newArray;
    }
}
