<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

class Route
{
    /**
     * @param non-empty-string                      $name
     * @param array<string, mixed>|non-empty-string $parameters
     * @param bool|non-empty-string                 $absolute
     * @param non-empty-string|null                 $generator
     */
    public function __construct(
        public readonly string $name,
        public readonly string|array $parameters = [],
        public readonly string|bool $absolute = false,
        public readonly ?string $generator = null,
    ) {}
}
