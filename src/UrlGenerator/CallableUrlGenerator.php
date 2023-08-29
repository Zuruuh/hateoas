<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator;

use Closure;

final class CallableUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @param Closure(string, array<string, mixed>, bool): string $callback
     */
    public function __construct(
        private readonly Closure $callback
    ) {}

    /**
     * {@inheritDoc}
     */
    public function generate(string $name, array $parameters, bool $absolute = false): string
    {
        return ($this->callback)($name, $parameters, $absolute);
    }
}
