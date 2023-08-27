<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata;

interface ConfigurationExtensionInterface
{
    public function decorate(ValueWithRelations $classMetadata): void;
}
