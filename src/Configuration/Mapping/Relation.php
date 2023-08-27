<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Relation
{
    /**
     * @param array<array-key, mixed> $attributes
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $href = null,
        public readonly mixed $embedded = null,
        public readonly array $attributes = [],
        public readonly ?Exclusion $exclusion = null,
    ) {}
}
