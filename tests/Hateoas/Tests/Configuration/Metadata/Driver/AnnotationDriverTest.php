<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Zuruuh\Hateoas\Configuration\Metadata\Driver\AnnotationDriver;

class AnnotationDriverTest extends AbstractDriverTest
{
    public function createDriver()
    {
        return new AnnotationDriver(
            new AnnotationReader(),
            $this->getExpressionEvaluator(),
            $this->createProvider(),
            $this->createTypeParser()
        );
    }
}
