<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\UrlGenerator;

use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zuruuh\Hateoas\Tests\TestCase;
use Zuruuh\Hateoas\UrlGenerator\SymfonyUrlGenerator;

/**
 * @internal
 *
 * @coversNothing
 */
class SymfonyUrlGeneratorTest extends TestCase
{
    use ProphecyTrait;

    public function test(): void
    {
        $name = 'user_get';
        $parameters = ['id' => 42];
        $absolute = true;
        $expectedResult = '/users/42';

        if (1 === UrlGeneratorInterface::ABSOLUTE_PATH) {
            $absolute = UrlGeneratorInterface::ABSOLUTE_URL;
        }

        $symfonyUrlGeneratorProphecy = $this->prophesize('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $symfonyUrlGeneratorProphecy
            ->generate($name, $parameters, $absolute)
            ->willReturn($expectedResult)
        ;

        $urlGenerator = new SymfonyUrlGenerator($symfonyUrlGeneratorProphecy->reveal());

        $this->assertSame(
            $expectedResult,
            $urlGenerator->generate($name, $parameters, $absolute)
        );
    }
}
