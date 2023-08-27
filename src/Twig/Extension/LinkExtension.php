<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Zuruuh\Hateoas\Helper\LinkHelper;

final class LinkExtension extends AbstractExtension
{
    public function __construct(private readonly LinkHelper $linkHelper) {}

    /**
     * {inheritDoc}.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('link_href', $this->linkHelper->getLinkHref(...)),
        ];
    }

    public function getName(): string
    {
        return 'hateoas_link';
    }
}
