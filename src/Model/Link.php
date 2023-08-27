<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Model;

class Link
{
    /**
     * @param array  $attributes
     */
    public function __construct(private readonly string $rel, private readonly string $href, private readonly array $attributes = [])
    {
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getRel(): string
    {
        return $this->rel;
    }
}
