<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

class NoAnnotations
{
    private string $unused = 'N/A';

    public function __construct(private readonly string $id, private readonly int $number)
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function number(): int
    {
        return $this->number;
    }
}
