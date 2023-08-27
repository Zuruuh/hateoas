<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration;

use JMS\Serializer\Expression\Expression;

class Embedded
{
    /**
     * @param string|mixed $content
     * @param string|Expression|null  $xmlElementName
     */
    public function __construct(private $content, private $xmlElementName = null, private readonly ?\Zuruuh\Hateoas\Configuration\Exclusion $exclusion = null, private readonly ?array $type = null)
    {
    }

    public function getType(): ?array
    {
        return $this->type;
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return Expression|string|null
     */
    public function getXmlElementName()
    {
        return $this->xmlElementName;
    }

    public function getExclusion(): ?Exclusion
    {
        return $this->exclusion;
    }
}
