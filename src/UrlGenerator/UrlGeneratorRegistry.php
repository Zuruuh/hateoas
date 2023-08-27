<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator;

use InvalidArgumentException;

class UrlGeneratorRegistry
{
    final public const DEFAULT_URL_GENERATOR_KEY = 'default';

    /**
     * @var array<string, UrlGeneratorInterface> $urlGenerators
     */
    private array $urlGenerators = [];

    public function __construct(?UrlGeneratorInterface $defaultUrlGenerator = null)
    {
        if ($defaultUrlGenerator instanceof UrlGeneratorInterface) {
            $this->urlGenerators = [self::DEFAULT_URL_GENERATOR_KEY => $defaultUrlGenerator];
        }
    }

    /**
     * @param $name If null it will return the default url generator
     * @throws InvalidArgumentException
     */
    public function get(?string $name = null): UrlGeneratorInterface
    {
        $name ??= self::DEFAULT_URL_GENERATOR_KEY;

        if (!array_key_exists($name, $this->urlGenerators)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The "%s" url generator is not set. Available url generators are: %s.',
                    $name,
                    implode(', ', array_keys($this->urlGenerators))
                )
            );
        }

        return $this->urlGenerators[$name];
    }

    public function set(string $name, UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerators[$name] = $urlGenerator;
    }

    public function hasGenerators(): bool
    {
        return [] !== $this->urlGenerators;
    }
}
