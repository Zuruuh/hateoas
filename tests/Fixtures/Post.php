<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Mapping as Hateoas;

#[Hateoas\SelfRelation(
    href: new Hateoas\Route(
        name: 'post_get',
        parameters: [
            'id' => 'expr(object.getId())',
        ],
    )
)]
final class Post
{
    public function __construct(private readonly int $id) {}

    public function getId(): int
    {
        return $this->id;
    }
}
