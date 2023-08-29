<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator\Registry;

use InvalidArgumentException;
use Zuruuh\Hateoas\UrlGenerator\UrlGeneratorInterface;

interface UrlGeneratorRegistryInterface
{
    public const DEFAULT_URL_GENERATOR_KEY = 'default';

    /**
     * @param $name If null it will return the default url generator
     *
     * @throws InvalidArgumentException
     */
    public function get(string $name = null): UrlGeneratorInterface;

    public function set(string $name, UrlGeneratorInterface $urlGenerator): void;

    public function hasGenerators(): bool;
}
