<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

#[Hateoas\Relation(
    name: 'self',
    href: new Hateoas\Route(
        name: 'user_get',
        parameters: [
            'id' => 'expr(object.getId())',
        ],
    )
)]
#[Hateoas\Relation(
    name: 'post',
    href: 'expr(link(object.getPost(), \'self\', true))',
)]
final class Will
{
    public function __construct(private readonly int $id, private readonly ?Post $post = null) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }
}
