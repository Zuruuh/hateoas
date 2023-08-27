<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}))
 * @Hateoas\Relation(
 *     "post",
 *     href = "expr(link(object.getPost(), 'self', true))"
 * )
 */
class Will
{
    public function __construct(private $id, private readonly ?Post $post = null) {}

    public function getId()
    {
        return $this->id;
    }

    public function getPost()
    {
        return $this->post;
    }
}
