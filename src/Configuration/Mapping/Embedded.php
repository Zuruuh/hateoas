<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Mapping;

class Embedded
{
    public function __construct(
        public readonly mixed $content,
        public readonly ?string $type = null,
        public readonly ?string $xmlElementName = null,
        public readonly ?Exclusion $exclusion = null,
    ) {}
}
