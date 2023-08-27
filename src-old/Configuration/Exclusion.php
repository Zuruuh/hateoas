<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration;

use JMS\Serializer\Expression\ExpressionEvaluator;

class Exclusion
{
    private ?string $sinceVersion = null;

    private ?string $untilVersion = null;

    private ?int $maxDepth = null;

    /**
     * @param mixed $excludeIf
     */
    public function __construct(
        private readonly ?array $groups = null,
        ?string $sinceVersion = null,
        ?string $untilVersion = null,
        ?int $maxDepth = null,
        private $excludeIf = null
    ) {
        $this->sinceVersion = $sinceVersion ?? null;
        $this->untilVersion = $untilVersion ?? null;
        $this->maxDepth = $maxDepth ?? null;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function getSinceVersion(): ?string
    {
        return $this->sinceVersion;
    }

    public function getUntilVersion(): ?string
    {
        return $this->untilVersion;
    }

    public function getMaxDepth(): ?int
    {
        return $this->maxDepth;
    }

    /**
     * @return null|ExpressionEvaluator|mixed|string
     */
    public function getExcludeIf()
    {
        return $this->excludeIf;
    }
}
