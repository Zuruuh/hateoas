<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Metadata\Driver;

use Metadata\Driver\FileLocator;
use Zuruuh\Hateoas\Configuration\Metadata\Driver\YamlDriver;

/**
 * @internal
 *
 * @coversNothing
 */
class YamlDriverTest extends AbstractDriverTest
{
    public function createDriver(): YamlDriver
    {
        return new YamlDriver(new FileLocator([
            'Hateoas\Tests\Fixtures' => $this->rootPath().'/Fixtures/config',
        ]), $this->getExpressionEvaluator(), $this->createProvider(), $this->createTypeParser());
    }
}
