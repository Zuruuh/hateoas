<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\UrlGenerator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class SymfonyUrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private readonly SymfonyUrlGeneratorInterface $urlGenerator
    ) {}

    public function generate(string $name, array $parameters, bool $absolute = false): string
    {
        $absolute = $absolute
            ? SymfonyUrlGeneratorInterface::ABSOLUTE_URL
            : SymfonyUrlGeneratorInterface::ABSOLUTE_PATH;

        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }
}
