<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Mapping;

class Exclusion
{
    /**
     * @param list<string> $groups
     */
    public function __construct(
        public readonly array $groups = [],
        public readonly ?string $sinceVersion = null,
        public readonly ?string $untilVersion = null,
        public readonly ?int $maxDepth = null,
        public readonly ?string $excludeIf = null,
    ) {}
}
