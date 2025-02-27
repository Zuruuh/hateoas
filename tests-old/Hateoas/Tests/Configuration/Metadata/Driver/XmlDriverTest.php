<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Metadata\Driver;

use Metadata\Driver\FileLocator;
use Zuruuh\Hateoas\Configuration\Metadata\Driver\XmlDriver;

/**
 * @internal
 *
 * @coversNothing
 */
class XmlDriverTest extends AbstractDriverTest
{
    public function createDriver(): XmlDriver
    {
        return new XmlDriver(new FileLocator([
            'Hateoas\Tests\Fixtures' => $this->rootPath().'/Fixtures/config',
        ]), $this->getExpressionEvaluator(), $this->createProvider(), $this->createTypeParser());
    }
}
