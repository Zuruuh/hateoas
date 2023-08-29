<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Zuruuh\Hateoas\Href\HrefFactoryInterface;

final class HateoasLinkExtension extends AbstractExtension
{
    public function __construct(private readonly HrefFactoryInterface $hrefFactory) {}

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('link_href', $this->hrefFactory->getLinkHref(...)),
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
