<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RelationProvider
{
    /**
     * @param null|non-empty-string $name
     */
    public function __construct(public readonly ?string $name = null) {}
}
