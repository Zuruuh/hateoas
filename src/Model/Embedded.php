<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Model;

// use Zuruuh\Hateoas\Serializer\Metadata\RelationPropertyMetadata;
// TODO: Re-implement

class Embedded
{
    public function __construct(
        public readonly string $rel,
        public readonly mixed $data,
        public readonly mixed /*RelationPropertyMetadata*/ $metadata,
        public readonly ?string $xmlElementName = null,
        public readonly ?array $type = null
    ) {}
}
