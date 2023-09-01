<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Symfony\Component\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Mapping as Hateoas;

final class CircularReference1
{
    public string $name = 'reference1';

    #[Serializer\Ignore]
    private mixed $reference2;

    #[Hateoas\Relation(name: 'reference2')]
    public function setReference2(mixed $reference2): void
    {
        $this->reference2 = $reference2;
    }

    public function getReference2(): mixed
    {
        return $this->reference2;
    }
}
