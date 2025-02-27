<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Zuruuh\Hateoas\Href\HrefFactoryInterface;

/**
 * @api
 */
final class HateoasLinkExtension extends AbstractExtension
{
    public function __construct(private readonly HrefFactoryInterface $hrefFactory) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('link_href', $this->hrefFactory->getLinkHref(...)),
        ];
    }

    public function getName(): string
    {
        return 'hateoas_link';
    }
}
