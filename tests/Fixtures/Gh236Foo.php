<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Symfony\Component\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Mapping as Hateoas;

final class Gh236Foo
{
    #[Serializer\MaxDepth(1)]
    public readonly Gh236Bar $a;

    #[Serializer\Ignore]
    #[Serializer\MaxDepth(1)]
    #[Hateoas\Relation(
        name: 'b_embed',
        embedded: new Hateoas\Embedded(
            exclusion: new Hateoas\Exclusion(
                maxDepth: 1,
            )
        )
    )]
    public readonly Gh236Bar $b;

    public function __construct()
    {
        $this->a = new Gh236Bar();
        $this->a->inner = new Gh236Bar();

        $this->b = new Gh236Bar();
        $this->b->xxx = 'zzz';
        $this->b->inner = new Gh236Bar();
    }
}
