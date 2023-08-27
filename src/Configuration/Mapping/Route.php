<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Mapping;

class Route
{
    /**
     * @param array<array-key, mixed> $parameters
     */
    public function __construct(
        public readonly string $name,
        public readonly array $parameters = [],
        public readonly bool $absolute = false,
        public readonly ?string $generator = null,
    ) {}
}
