<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

final class LinkExpressionFunction implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction(
                'link',
                static fn ($object, $rel, $absolute = false) => sprintf('$link_helper->getLinkHref(%s, %s, %s)', $object, $rel, $absolute),
                static fn ($context, $object, $rel, $absolute = false) => $context['link_helper']->getLinkHref($object, $rel, $absolute),
            ),
        ];
    }
}
