<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Hateoas\Relation(
 *     "self1",
 *     href = "foo1",
 *     embedded = "foo1"
 * )
 */
class Foo1
{
    /**
     * @Serializer\Inline
     */
    public $inline;
}
