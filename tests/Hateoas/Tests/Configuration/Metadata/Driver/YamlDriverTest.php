<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Metadata\Driver;

use Zuruuh\Hateoas\Configuration\Metadata\Driver\YamlDriver;
use Metadata\Driver\FileLocator;

class YamlDriverTest extends AbstractDriverTest
{
    public function createDriver(): \Zuruuh\Hateoas\Configuration\Metadata\Driver\YamlDriver
    {
        return new YamlDriver(new FileLocator([
            'Hateoas\Tests\Fixtures' => $this->rootPath() . '/Fixtures/config',
        ]), $this->getExpressionEvaluator(), $this->createProvider(), $this->createTypeParser());
    }
}
