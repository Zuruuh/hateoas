<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration;

use Zuruuh\Hateoas\Expression\Expression;

class Route
{
    /**
     * @param array<string, string>|string      $parameters
     */
    public function __construct(
        public readonly Expression|string $name,
        public readonly array|string $parameters = [],
        public readonly bool $isAbsolute = false,
        public readonly ?string $generator = null
    ) {}
}
