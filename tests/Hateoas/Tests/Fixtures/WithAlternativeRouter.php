<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "search",
 *      href = @Hateoas\Route(
 *          "/search",
 *          parameters = {
 *              "query" = "hello"
 *          },
 *          generator = "my_generator"
 *      )
 * )
 */
class WithAlternativeRouter
{
}
