<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Mapping;

class Embedded
{
    /**
     * @param non-empty-string|null $type
     * @param non-empty-string|null $nodeName
     */
    public function __construct(
        public readonly mixed $content,
        public readonly ?string $type = null,
        public readonly ?string $nodeName = null,
        public readonly ?Exclusion $exclusion = null,
    ) {}
}
