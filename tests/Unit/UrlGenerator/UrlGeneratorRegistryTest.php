<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Unit\UrlGenerator;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Throwable;
use Zuruuh\Hateoas\UrlGenerator\Registry\UrlGeneratorRegistry;
use Zuruuh\Hateoas\UrlGenerator\UrlGeneratorInterface;

#[CoversClass(UrlGeneratorRegistry::class)]
final class UrlGeneratorRegistryTest extends TestCase
{
    public function test(): void
    {
        $defaultUrlGenerator = $this->mockUrlGenerator();
        $registry = new UrlGeneratorRegistry($defaultUrlGenerator);

        self::assertEquals($defaultUrlGenerator, $registry->get(UrlGeneratorRegistry::DEFAULT_URL_GENERATOR_KEY));
        self::assertEquals($defaultUrlGenerator, $registry->get());

        $exception = null;

        try {
            $registry->get('foo');
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(InvalidArgumentException::class, $exception);
        self::assertEquals(
            'The "foo" url generator is not set. Available url generators are: default.',
            $exception->getMessage()
        );

        $fooUrlGenerator = self::mockUrlGenerator();
        $registry->set('foo', $fooUrlGenerator);
        self::assertEquals($fooUrlGenerator, $registry->get('foo'));
    }

    private function mockUrlGenerator(): UrlGeneratorInterface
    {
        return $this->createMock(UrlGeneratorInterface::class);
    }
}
