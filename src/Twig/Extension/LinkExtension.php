<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Zuruuh\Hateoas\Helper\LinkHelper;

class LinkExtension extends AbstractExtension
{
    public function __construct(private readonly \Zuruuh\Hateoas\Helper\LinkHelper $linkHelper) {}

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('link_href', $this->linkHelper->getLinkHref(...)),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'hateoas_link';
    }
}
