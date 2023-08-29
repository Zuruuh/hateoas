<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Href;

use RuntimeException;

interface HrefFactoryInterface
{
    /**
     * @throws RuntimeException
     */
    public function getLinkHref(object $object, string $rel, bool $absolute = false): string;
}
