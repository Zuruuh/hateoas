<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

class Embedded
{
    /**
     * @param non-empty-string|null              $content
     * @param non-empty-string|class-string|null $type
     * @param non-empty-string|null              $nodeName
     */
    public function __construct(
        public readonly ?string $content = null,
        public readonly ?string $type = null,
        public readonly ?string $nodeName = null,
        public readonly ?Exclusion $exclusion = null,
    ) {}
}
