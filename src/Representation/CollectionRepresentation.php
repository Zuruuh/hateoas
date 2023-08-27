<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Representation;

use JMS\Serializer\Annotation as Serializer;
use Traversable;
use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Serializer\XmlRoot("collection")
 *
 * @Hateoas\Relation(
 *     "items",
 *     embedded = @Hateoas\Embedded("expr(object.getResources())")
 * )
 */
class CollectionRepresentation
{
    /**
     * @var mixed
     */
    private $resources;

    /**
     * @param array|Traversable $resources
     */
    public function __construct($resources)
    {
        if ($resources instanceof Traversable) {
            $resources = iterator_to_array($resources);
        }

        $this->resources = $resources;
    }

    /**
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }
}
