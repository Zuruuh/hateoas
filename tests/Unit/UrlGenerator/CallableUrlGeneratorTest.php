<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Unit\UrlGenerator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Zuruuh\Hateoas\UrlGenerator\CallableUrlGenerator;

#[CoversClass(CallableUrlGenerator::class)]
final class CallableUrlGeneratorTest extends TestCase
{
    public function testCallableUrlGenerator(): void
    {
        $expectedName = 'user_get';
        $expectedParameters = ['id' => 42];
        $expectedAbsolute = true;
        $expectedResult = '/users/42';

        $callable = function ($name, $parameters, $absolute) use ($expectedName, $expectedParameters, $expectedResult, $expectedAbsolute) {
            self::assertEquals($expectedName, $name);
            self::assertEquals($expectedParameters, $parameters);
            self::assertEquals($expectedAbsolute, $absolute);

            return $expectedResult;
        };
        $urlGenerator = new CallableUrlGenerator($callable);

        self::assertEquals(
            $expectedResult,
            $urlGenerator->generate($expectedName, $expectedParameters, $expectedAbsolute)
        );
    }
}
