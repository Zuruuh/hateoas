<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration;

use JMS\Serializer\Expression\Expression;

class Route
{
    /**
     * @param string|Expression $name
     * @param string|array $parameters
     * @param bool|Expression $absolute
     * @param string|null $generator
     */
    public function __construct(private $name, private $parameters = [], private $absolute = false, private readonly ?string $generator = null) {}

    /**
     * @return Expression|string|array|string[]|Expression[]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return bool|Expression
     */
    public function isAbsolute()
    {
        return $this->absolute;
    }

    public function getGenerator(): ?string
    {
        return $this->generator;
    }
}
