<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration;

final class RelationProvider
{
    public function __construct(public readonly string $name) {}
}
