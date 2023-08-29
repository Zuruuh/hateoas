<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RelationProvider
{
    /**
     * @param class-string $class If null, will default to targeted class
     */
    public function __construct(
        public readonly ?string $class = null,
        public readonly string $method = '__invoke',
    ) {}
}
