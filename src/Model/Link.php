<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Model;

class Link
{
    /**
     * @param array<array-key, mixed> $attributes
     */
    public function __construct(
        public readonly string $rel,
        public readonly string $href,
        public readonly array $attributes = []
    ) {}
}
