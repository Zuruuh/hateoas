<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @api
 */
class Route
{
    /**
     * @param non-empty-string|Expression                      $name
     * @param non-empty-string|Expression|array<string, mixed> $parameters
     * @param non-empty-string|Expression|bool                 $absolute
     * @param non-empty-string|Expression|null                 $generator
     */
    public function __construct(
        public readonly string|Expression $name,
        public readonly string|Expression|array $parameters = [],
        public readonly string|Expression|bool $absolute = false,
        public readonly string|Expression|null $generator = null,
    ) {}
}
