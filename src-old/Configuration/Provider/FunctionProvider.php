<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Provider;

use Zuruuh\Hateoas\Configuration\RelationProvider;

class FunctionProvider implements RelationProviderInterface
{
    public function getRelations(RelationProvider $configuration, string $class): array
    {
        if (!preg_match('/func\((?P<function>.+)\)/i', (string) $configuration->getName(), $matches)) {
            return [];
        }

        return call_user_func($matches['function'], $class);
    }
}
