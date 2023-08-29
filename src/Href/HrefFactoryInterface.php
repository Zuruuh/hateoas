<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Href;

interface HrefFactoryInterface
{
    public function getLinkHref(object $object, string $rel, bool $absolute = false): string;
}
