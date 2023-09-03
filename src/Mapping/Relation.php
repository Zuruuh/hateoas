<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

use Attribute;

/**
 * @api
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Relation
{
    /**
     * @param non-empty-string            $name
     * @param non-empty-string|Route|null $href
     * @param array<string, mixed>        $attributes
     */
    public function __construct(
        public readonly string $name,
        public readonly null|string|Route $href = null,
        public readonly mixed $embedded = null,
        public readonly array $attributes = [],
        public readonly ?Exclusion $exclusion = null,
    ) {}
}
