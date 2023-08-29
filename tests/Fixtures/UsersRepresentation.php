<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

final class UsersRepresentation
{
    public function __construct(
        public readonly mixed $inline
    ) {}
}
