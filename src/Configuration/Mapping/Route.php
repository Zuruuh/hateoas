<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Mapping;

class Route
{
    /**
     * @param non-emptry-string $name
     * @param array<array-key, mixed> $parameters
     * @param non-empty-string|bool $absolute
     * @param non-empty-string|null $generator
     */
    public function __construct(
        public readonly string $name,
        public readonly array $parameters = [],
        public readonly string|bool $absolute = false,
        public readonly ?string $generator = null,
    ) {}
}
