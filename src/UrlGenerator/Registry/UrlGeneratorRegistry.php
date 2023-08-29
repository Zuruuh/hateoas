<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator\Registry;

use InvalidArgumentException;
use Zuruuh\Hateoas\UrlGenerator\UrlGeneratorInterface;

final class UrlGeneratorRegistry implements UrlGeneratorRegistryInterface
{
    /**
     * @var array<string, UrlGeneratorInterface>
     */
    private array $urlGenerators = [];

    public function __construct(UrlGeneratorInterface $defaultUrlGenerator = null)
    {
        if ($defaultUrlGenerator instanceof UrlGeneratorInterface) {
            $this->urlGenerators = [self::DEFAULT_URL_GENERATOR_KEY => $defaultUrlGenerator];
        }
    }

    public function get(string $name = null): UrlGeneratorInterface
    {
        $name ??= self::DEFAULT_URL_GENERATOR_KEY;

        if (!array_key_exists($name, $this->urlGenerators)) {
            throw new InvalidArgumentException(sprintf('The "%s" url generator is not set. Available url generators are: %s.', $name, implode(', ', array_keys($this->urlGenerators))));
        }

        return $this->urlGenerators[$name];
    }

    public function set(string $name, UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerators[$name] = $urlGenerator;
    }

    public function hasGenerators(): bool
    {
        return $this->urlGenerators !== [];
    }
}
