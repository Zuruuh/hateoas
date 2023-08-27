<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration;

use Zuruuh\Hateoas\Expression\Expression;

class Relation
{
    /**
     * @param                         $name       The link "rel" attribute
     * @param list<Expression|string> $attributes
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public readonly string|Expression $name,
        public readonly string|Route|null $href = null,
        public readonly Embedded|string|null $embedded = null,
        public readonly array $attributes = [],
        public readonly ?Exclusion $exclusion = null,
    ) {
        if (null !== $this->embedded && !$this->embedded instanceof Embedded) {
            $this->embedded = new Embedded($this->embedded);
        }

        if (null === !$href && null === $embedded) {
            throw new \InvalidArgumentException('$href and $embedded cannot be both null.');
        }
    }
}
