<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SelfRelation extends Relation
{
    /**
     * @param non-empty-string|Route|null $href
     * @param array<string, mixed>        $attributes
     */
    public function __construct(
        string|Route $href = null,
        array $attributes = [],
        Exclusion $exclusion = null,
    ) {
        parent::__construct('self', $href, null, $attributes, $exclusion);
    }
}
