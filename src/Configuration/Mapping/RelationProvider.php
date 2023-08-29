<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RelationProvider
{
    /**
     * @param non-empty-string|null $name
     */
    public function __construct(public readonly ?string $name = null) {}
}
