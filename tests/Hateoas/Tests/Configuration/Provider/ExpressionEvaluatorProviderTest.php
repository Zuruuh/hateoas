<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Provider;

use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;
use Zuruuh\Hateoas\Configuration\Provider\ExpressionEvaluatorProvider;
use Zuruuh\Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Zuruuh\Hateoas\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ExpressionEvaluatorProviderTest extends TestCase
{
    use ProphecyTrait;

    public function test(): void
    {
        $service = new stdClass();

        $containerProphecy = $this->prophesize(ExpressionEvaluatorInterface::class);
        $containerProphecy
            ->evaluate('foo', ['class' => stdClass::class])
            ->willReturn([$service])
        ;

        $providerProvider = new ExpressionEvaluatorProvider($containerProphecy->reveal());

        $this->assertEmpty($providerProvider->getRelations(new RelationProviderConfiguration('!-;'), stdClass::class));
        $this->assertEmpty($providerProvider->getRelations(new RelationProviderConfiguration('getSomething'), stdClass::class));
        $this->assertEmpty($providerProvider->getRelations(new RelationProviderConfiguration('foo::bar'), stdClass::class));
        $this->assertSame(
            [$service],
            $providerProvider->getRelations(new RelationProviderConfiguration('expr(foo)'), stdClass::class)
        );
    }
}
