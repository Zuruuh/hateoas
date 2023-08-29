<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator;

interface UrlGeneratorInterface
{
    /**
     * @param non-empty-string     $name
     * @param array<string, mixed> $parameters
     */
    public function generate(string $name, array $parameters, bool $absolute = false): string;
}
