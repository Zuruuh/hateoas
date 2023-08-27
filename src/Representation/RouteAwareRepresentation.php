<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Representation;

use JMS\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters())",
 *          absolute = "expr(object.isAbsolute())"
 *      )
 * )
 */
class RouteAwareRepresentation
{
    /**
     * @param mixed $inline
     * @param array $parameters
     */
    public function __construct(private $inline, private readonly string $route, private readonly array $parameters = [], private readonly bool $absolute = false) {}

    /**
     * @return mixed
     */
    public function getInline()
    {
        return $this->inline;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function isAbsolute(): bool
    {
        return $this->absolute;
    }
}
