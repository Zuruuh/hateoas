<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Configuration\Metadata\Driver;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Zuruuh\Hateoas\Configuration\Metadata\ClassMetadata;
use Zuruuh\Hateoas\Configuration\Metadata\Driver\ExtensionDriver;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Tests\TestCase;

class ExtensionDriverTest extends TestCase
{
    use ProphecyTrait;

    public function testDoesNothingIfNoExtension(): void
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $classMetadata = new ClassMetadata(static::class);
        $loadMetadataForClassCallCount = 0;

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->will(function () use (&$loadMetadataForClassCallCount, $classMetadata): ?\Zuruuh\Hateoas\Configuration\Metadata\ClassMetadata {
                return $loadMetadataForClassCallCount++ < 1 ? $classMetadata : null;
            })
            ->shouldBeCalledTimes(2);
        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), []);

        $this->assertSame($classMetadata, $extensionDriver->loadMetadataForClass($reflectionClass));
        $this->assertNull($extensionDriver->loadMetadataForClass($reflectionClass));
    }

    public function testExtensions(): void
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $classMetadata = new ClassMetadata(static::class);

        $extensions = [];
        for ($i = 0; $i < 2; $i++) {
            $extensionProphecy = $this->prophesize('Hateoas\Configuration\Metadata\ConfigurationExtensionInterface');
            $extensionProphecy
                ->decorate($classMetadata)
                ->shouldBeCalledTimes(1);
            $extensions[] = $extensionProphecy->reveal();
        }

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->willReturn($classMetadata);

        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), $extensions);

        $this->assertSame($classMetadata, $extensionDriver->loadMetadataForClass($reflectionClass));
    }

    public function testDelegateReturnsNullAndNoExtensions(): void
    {
        $reflectionClass = new \ReflectionClass(static::class);

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->willReturn(null);

        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), []);

        $this->assertNull($extensionDriver->loadMetadataForClass($reflectionClass));
    }

    public function testDelegateReturnsNullAndExtensionsDoNothing(): void
    {
        $reflectionClass = new \ReflectionClass(static::class);

        $extensionProphecy = $this->prophesize('Hateoas\Configuration\Metadata\ConfigurationExtensionInterface');
        $extensionProphecy
            ->decorate(Argument::type('Hateoas\Configuration\Metadata\ClassMetadataInterface'))
            ->shouldBeCalledTimes(1);

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->willReturn(null);

        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), [$extensionProphecy->reveal()]);

        $this->assertNull($extensionDriver->loadMetadataForClass($reflectionClass));
    }

    public function testDelegateReturnsNullAndExtensionsAddRelations(): void
    {
        $reflectionClass = new \ReflectionClass(static::class);

        $extensionProphecy = $this->prophesize('Hateoas\Configuration\Metadata\ConfigurationExtensionInterface');
        $extensionProphecy
            ->decorate(Argument::type('Hateoas\Configuration\Metadata\ClassMetadataInterface'))
            ->will(function (array $args): void {
                $args[0]->addRelation(new Relation('foo', 'bar'));
            })
            ->shouldBeCalledTimes(1);

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->willReturn(null);

        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), [$extensionProphecy->reveal()]);

        $this->assertInstanceOf(
            'Hateoas\Configuration\Metadata\ClassMetadataInterface',
            $extensionDriver->loadMetadataForClass($reflectionClass)
        );
    }
}
