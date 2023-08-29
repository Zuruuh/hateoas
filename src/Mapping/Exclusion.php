<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

class Exclusion
{
    /**
     * @param list<string>          $groups
     * @param null|non-empty-string $sinceVersion
     * @param null|non-empty-until  $untilVersion
     * @param null|positive-int     $maxDepth
     * @param null|non-empty-string $excludeIf
     */
    public function __construct(
        public readonly array $groups = [],
        public readonly ?string $sinceVersion = null,
        public readonly ?string $untilVersion = null,
        public readonly ?int $maxDepth = null,
        public readonly ?string $excludeIf = null,
    ) {}
}
