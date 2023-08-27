<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator;

interface UrlGeneratorInterface
{
    /**
     * @param array<string, mixed>
     */
    public function generate(string $name, array $parameters, bool $absolute = false): string;
}
