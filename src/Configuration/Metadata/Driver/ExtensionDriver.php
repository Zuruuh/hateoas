<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata\Driver;

use Metadata\ClassMetadata as JMSClassMetadata;
use Metadata\Driver\DriverInterface;
use Zuruuh\Hateoas\Configuration\Metadata\ClassMetadata;
use Zuruuh\Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;

class ExtensionDriver implements DriverInterface
{
    /**
     * @param \Zuruuh\Hateoas\Configuration\Metadata\ConfigurationExtensionInterface[] $extensions
     */
    public function __construct(private readonly \Metadata\Driver\DriverInterface $delegate, private array $extensions = []) {}

    public function loadMetadataForClass(\ReflectionClass $class): ?JMSClassMetadata
    {
        $metadata    = $this->delegate->loadMetadataForClass($class);
        $newMetadata = false;

        if ($this->extensions === []) {
            return $metadata;
        }

        if (!$metadata instanceof \Metadata\ClassMetadata) {
            $metadata    = new ClassMetadata($class->getName());
            $newMetadata = true;
        }

        foreach ($this->extensions as $extension) {
            $extension->decorate($metadata);
        }

        if ($newMetadata && count($metadata->getRelations()) < 1) {
            $metadata = null;
        }

        return $metadata;
    }

    public function registerExtension(ConfigurationExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
    }
}
