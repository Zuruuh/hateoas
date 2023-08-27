<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration;

use Zuruuh\Hateoas\Expression\Expression;

class Embedded
{
    private string $content;

    private string|Expression|null $xmlElementName;

    private ?Exclusion $exclusion;

    /
    private array $type;

    /**
     * @param array<array-key, mixed> $type
     */
    public function __construct(
        public readonly string $content,
        public readonly string|Expression|null $xmlElementName = null,
        public readonly ?Exclusion $exclusion = null,
        public readonly ?array $type = null
    ) {}
}
