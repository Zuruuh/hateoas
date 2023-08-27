<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Metadata\Driver;

use Zuruuh\Hateoas\Configuration\Metadata\Driver\XmlDriver;
use Metadata\Driver\FileLocator;

class XmlDriverTest extends AbstractDriverTest
{
    public function createDriver()
    {
        return new XmlDriver(new FileLocator([
            'Hateoas\Tests\Fixtures' => $this->rootPath() . '/Fixtures/config',
        ]), $this->getExpressionEvaluator(), $this->createProvider(), $this->createTypeParser());
    }
}
