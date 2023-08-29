<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Relation
{
    /**
     * @param non-empty-string        $name
     * @param null|non-empty-string   $href
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
