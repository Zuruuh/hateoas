<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Unit\Normalizer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Zuruuh\Hateoas\Normalizer\HypermediaNormalizer;
use Zuruuh\Hateoas\Tests\Fixtures\AdrienBrault;
use Zuruuh\Hateoas\Tests\Helper\HypermediaNormalizerAware;

#[CoversClass(HypermediaNormalizer::class)]
#[Group('debug')]
final class HypermediaNormalizerTest extends TestCase
{
    use HypermediaNormalizerAware;

    public function testHypermediaNormalization(): void
    {
        dump($this->provideHypermediaNormalizer()->normalize(new AdrienBrault()));
    }
}
