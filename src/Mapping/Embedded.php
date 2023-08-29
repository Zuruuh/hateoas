<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

class Embedded
{
    /**
     * @param null|non-empty-string $type
     * @param null|non-empty-string $nodeName
     */
    public function __construct(
        public readonly mixed $content,
        public readonly ?string $type = null,
        public readonly ?string $nodeName = null,
        public readonly ?Exclusion $exclusion = null,
    ) {}
}
