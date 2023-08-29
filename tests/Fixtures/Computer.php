<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

final class Computer
{
    public function __construct(public readonly mixed $name) {}
}
