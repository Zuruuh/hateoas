<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * Demonstrates how to override the xml root name of a PaginatedCollection
 *
 * @Serializer\XmlRoot("users")
 */
class UsersRepresentation
{
    public function __construct(
        /**
         * @Serializer\Inline
         */
        private $inline
    )
    {
    }
}
