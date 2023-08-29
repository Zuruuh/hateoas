<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

class Route
{
    /**
     * @param non-emptry-string       $name
     * @param array<array-key, mixed> $parameters
     * @param bool|non-empty-string   $absolute
     * @param null|non-empty-string   $generator
     */
    public function __construct(
        public readonly string $name,
        public readonly array $parameters = [],
        public readonly string|bool $absolute = false,
        public readonly ?string $generator = null,
    ) {}
}
