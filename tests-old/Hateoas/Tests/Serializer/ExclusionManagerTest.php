<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Serializer;

use Closure;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Metadata\MetadataFactoryInterface;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Zuruuh\Hateoas\Configuration\Exclusion;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Serializer\ExclusionManager;
use Zuruuh\Hateoas\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ExclusionManagerTest extends TestCase
{
    use ProphecyTrait;

    public function testDoesNotSkipNonNullEmbedded(): void
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new stdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create();

        $this->assertFalse($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testSkipNullEmbedded(): void
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new stdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testDoesNotSkipNonNullLink(): void
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new stdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this->assertFalse($exclusionManager->shouldSkipLink($object, $relation, $context));
    }

    public function testSkipNullLink(): void
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new stdClass();
        $relation = new Relation('foo', null, 'foo');
        $context = SerializationContext::create();

        $this->assertTrue($exclusionManager->shouldSkipLink($object, $relation, $context));
    }

    public function testSkip(): void
    {
        $test = $this;
        $exclusionStrategyCallback = function (array $args) use ($test): void {
            $test->assertSame(['foo', 'bar'], $args[0]->groups);
            $test->assertSame('1.1', $args[0]->sinceVersion);
            $test->assertSame('1.7', $args[0]->untilVersion);
            $test->assertSame(77, $args[0]->maxDepth);
        };

        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));
        $exclusionStrategy = $this->mockExclusionStrategy(true, $exclusionStrategyCallback, 2);

        $object = new stdClass();
        $exclusion = new Exclusion(
            ['foo', 'bar'],
            '1.1',
            '1.7',
            77
        );
        $relation = new Relation('foo', 'foo', 'foo', [], $exclusion);
        $context = SerializationContext::create()
            ->addExclusionStrategy($exclusionStrategy)
        ;

        $this->assertTrue($exclusionManager->shouldSkipLink($object, $relation, $context));
        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testSkipEmbedded(): void
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new stdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create()
            ->addExclusionStrategy($this->mockExclusionStrategy(true))
        ;

        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    /**
     * @dataProvider getTestSkipExcludeIfData
     */
    public function testSkipExcludeIf(bool $exclude): void
    {
        $object = (object) ['name' => 'adrien'];
        $exclusion = new Exclusion(null, null, null, null, 'stuff');
        $relation = new Relation('foo', 'foo', 'foo', [], $exclusion);
        $context = SerializationContext::create();
        $context->initialize(
            'json',
            $this->prophesize(SerializationVisitorInterface::class)->reveal(),
            $this->prophesize(GraphNavigatorInterface::class)->reveal(),
            $this->prophesize(MetadataFactoryInterface::class)->reveal()
        );

        $context->startVisiting($object);

        $expressionEvaluatorProphecy = $this->prophesize(ExpressionEvaluatorInterface::class);
        $expressionEvaluatorProphecy
            ->evaluate('stuff', Argument::any())
            ->shouldBeCalled()
            ->willReturn($exclude)
        ;
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy($expressionEvaluatorProphecy->reveal()));

        $this->assertSame($exclude, $exclusionManager->shouldSkipLink($object, $relation, $context));
        $this->assertSame($exclude, $exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function getTestSkipExcludeIfData(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @param Closure $shouldSkipPropertyCallback
     * @param int     $calledTimes
     */
    private function mockExclusionStrategy(bool $shouldSkipProperty = false, $shouldSkipPropertyCallback = null, $calledTimes = null)
    {
        $exclusionStrategyProphecy = $this->prophesize('JMS\Serializer\Exclusion\ExclusionStrategyInterface');
        $method = $exclusionStrategyProphecy
            ->shouldSkipProperty(
                Argument::type('Hateoas\Serializer\Metadata\RelationPropertyMetadata'),
                Argument::type('JMS\Serializer\SerializationContext')
            )
            ->will(function () use ($shouldSkipProperty, $shouldSkipPropertyCallback): bool {
                if ($shouldSkipPropertyCallback instanceof Closure) {
                    call_user_func_array($shouldSkipPropertyCallback, func_get_args());
                }

                return $shouldSkipProperty;
            })
        ;

        if (null !== $calledTimes) {
            $method->shouldBeCalledTimes($calledTimes);
        }

        return $exclusionStrategyProphecy->reveal();
    }
}
