<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = @Hateoas\Route("post_get", parameters = {"id" = "expr(object.getId())"}))
 */
class Post
{
    public function __construct(private $id) {}

    public function getId()
    {
        return $this->id;
    }
}
