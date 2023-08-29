<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Unit\UrlGenerator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zuruuh\Hateoas\UrlGenerator\SymfonyUrlGenerator;

#[CoversClass(SymfonyUrlGenerator::class)]
final class SymfonyUrlGeneratorTest extends TestCase
{
    /**
     * @param non-empty-string $name
     * @param array<string, mixed> $parameters
     */
    #[DataProvider('provideCases')]
    #[Test]
    public function doTests(string $name, array $parameters, bool $absolute, string $expected): void
    {
        $mockUrlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $mockUrlGenerator
            ->method('generate')
            ->with($name, $parameters, $absolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn($expected)
        ;

        $urlGenerator = new SymfonyUrlGenerator($mockUrlGenerator);

        self::assertEquals(
            $expected,
            $urlGenerator->generate($name, $parameters, $absolute)
        );
    }

    /**
     * @return array<string, array{
     *     name: non-empty-string,
     *     parameters: array<string, mixed>,
     *     absolute: bool,
     *     expected: string,
     * }>
     */
    public static function provideCases(): array
    {
        return [
            'With absolute path' => [
                'name' => 'user_get',
                'parameters' => ['id' => 42],
                'absolute' => false,
                'expected' => '/users/42',
            ],
            'With absolute URL' => [
                'name' => 'user_get',
                'parameters' => ['id' => 42],
                'absolute' => true,
                'expected' => 'https://foo.bar/users/42',
            ],
        ];
    }
}
