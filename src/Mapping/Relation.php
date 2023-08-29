<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Relation
{
    /**
     * @param non-empty-string        $name
     * @param non-empty-string|null   $href
     * @param array<array-key, mixed> $attributes
     */
    public function __construct(
        public readonly string $name,
        public readonly null|string|Route $href = null,
        public readonly mixed $embedded = null,
        public readonly array $attributes = [],
        public readonly ?Exclusion $exclusion = null,
    ) {}
}
