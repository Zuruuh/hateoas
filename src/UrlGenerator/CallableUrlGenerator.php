<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator;

use Closure;

final class CallableUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @param callable(string, array<string, mixed>, bool): string $callable
     */
    public function __construct(
        private readonly Closure $callback
    ) {}

    /**
     * {@inheritDoc}
     */
    public function generate(string $name, array $parameters, bool $absolute = false): string
    {
        return $this->callback->__invoke($name, $parameters, $absolute);
    }
}
