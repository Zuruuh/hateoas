<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator\Registry;

use InvalidArgumentException;
use RuntimeException;
use Zuruuh\Hateoas\UrlGenerator\UrlGeneratorInterface;

/**
 * @api
 */
final class UrlGeneratorRegistry implements UrlGeneratorRegistryInterface
{
    /**
     * @var array<string, UrlGeneratorInterface>
     */
    private array $urlGenerators = [];

    public function __construct(private ?UrlGeneratorInterface $defaultUrlGenerator = null)
    {
    }

    public function get(string $name = null): UrlGeneratorInterface
    {
        if ($name === null) {
            if ($this->defaultUrlGenerator === null) {
                throw new InvalidArgumentException('Tried to provide the default url generator but none was provided in the first place!');
            }

            return $this->defaultUrlGenerator;
        }

        if (!array_key_exists($name, $this->urlGenerators)) {
            throw new InvalidArgumentException(sprintf('The "%s" url generator is not set. Available url generators are: %s.', $name, implode(', ', array_keys($this->urlGenerators))));
        }

        return $this->urlGenerators[$name];
    }

    public function setDefault(UrlGeneratorInterface $urlGenerator): void
    {
        $this->defaultUrlGenerator = $urlGenerator;
    }

    public function set(string $name, UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerators[$name] = $urlGenerator;
    }

    public function hasGenerators(): bool
    {
        return $this->urlGenerators !== [] || $this->defaultUrlGenerator !== null;
    }
}
