<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation("reference1", embedded="expr(object.getReference1())")
 */
class CircularReference2
{
    /**
     * @Serializer\Expose
     */
    private string $name = 'reference2';

    private $reference1;

    public function setReference1($reference1): void
    {
        $this->reference1 = $reference1;
    }

    public function getReference1()
    {
        return $this->reference1;
    }
}
