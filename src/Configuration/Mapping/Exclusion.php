<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Mapping;

class Exclusion
{
    /**
     * @param list<string> $groups
     * @param non-empty-string|null $sinceVersion
     * @param non-empty-until|null $untilVersion
     * @param positive-int|null $maxDepth
     * @param non-empty-string|null $excludeIf
     */
    public function __construct(
        public readonly array $groups = [],
        public readonly ?string $sinceVersion = null,
        public readonly ?string $untilVersion = null,
        public readonly ?int $maxDepth = null,
        public readonly ?string $excludeIf = null,
    ) {}
}
