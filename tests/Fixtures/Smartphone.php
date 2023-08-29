<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

final class Smartphone
{
    public function __construct(private readonly mixed $name) {}

    public function getName(): mixed
    {
        return $this->name;
    }
}
