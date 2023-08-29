<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

final class Gh236Bar
{
    public string $xxx = 'yyy';

    #[Serializer\Context([AbstractObjectNormalizer::SKIP_UNINITIALIZED_VALUES => true])]
    public ?self $inner;
}
