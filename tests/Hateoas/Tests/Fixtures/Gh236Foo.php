<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "b_embed",
 *     embedded = @Hateoas\Embedded(
 *         "expr(object.b)",
 *          exclusion = @Hateoas\Exclusion(maxDepth=1)
 *     )
 * )
 */
class Gh236Foo
{
    /**
     * @Serializer\Expose()
     * @Serializer\MaxDepth(1)
     * @var \Zuruuh\Hateoas\Tests\Fixtures\Gh236Bar
     */
    public $a;

    /**
     * @Serializer\Exclude()
     * @var \Zuruuh\Hateoas\Tests\Fixtures\Gh236Bar
     */
    public $b;

    public function __construct()
    {
        $this->a = new Gh236Bar();
        $this->a->inner = new Gh236Bar();

        $this->b = new Gh236Bar();
        $this->b->xxx = 'zzz';
        $this->b->inner = new Gh236Bar();
    }
}
