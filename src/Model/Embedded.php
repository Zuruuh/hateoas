<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Model;

use Zuruuh\Hateoas\Serializer\Metadata\RelationPropertyMetadata;

class Embedded
{
    /**
     * @param mixed $data
     * @param string|null $xmlElementName
     */
    public function __construct(private readonly string $rel, private $data, private readonly \Zuruuh\Hateoas\Serializer\Metadata\RelationPropertyMetadata $metadata, private readonly ?string $xmlElementName = null, private readonly ?array $type = null) {}

    public function getType(): ?array
    {
        return $this->type;
    }

    public function getMetadata(): RelationPropertyMetadata
    {
        return $this->metadata;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function getRel(): string
    {
        return $this->rel;
    }

    public function getXmlElementName(): ?string
    {
        return $this->xmlElementName;
    }
}
