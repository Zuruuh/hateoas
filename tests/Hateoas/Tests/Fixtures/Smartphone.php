<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("smartphone")
 */
class Smartphone
{
    public function __construct(private $name)
    {
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
