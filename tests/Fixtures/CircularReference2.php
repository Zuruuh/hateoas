<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Symfony\Component\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Mapping as Hateoas;

final class CircularReference2
{
    /**
     * @phpstan-ignore-next-line
     */
    #[Serializer\Ignore] private string $name = 'reference2';
    private self $reference1;

    #[Hateoas\Relation(name: 'reference1')]
    public function setReference1(self $reference1): void
    {
        $this->reference1 = $reference1;
    }

    public function getReference1(): self
    {
        return $this->reference1;
    }
}
