<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation("reference2", embedded="expr(object.getReference2())")
 */
class CircularReference1
{
    /**
     * @Serializer\Expose
     */
    private string $name = 'reference1';

    private $reference2;

    public function setReference2($reference2): void
    {
        $this->reference2 = $reference2;
    }

    public function getReference2()
    {
        return $this->reference2;
    }
}
