<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Factory;

use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Zuruuh\Hateoas\Configuration\Embedded;
use Zuruuh\Hateoas\Configuration\Metadata\ClassMetadata;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Factory\EmbeddedsFactory;
use Zuruuh\Hateoas\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class EmbeddedsFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function test(): void
    {
        $relations = [
            new Relation('self', '/users/1'),
            new Relation('friend', '/users/42', $this->expr('object.getFriend()')),
            new Relation(
                'manager',
                '/users/42',
                new Embedded($this->expr('object.getManager()'), $this->expr('object.getXmlElementName()'))
            ),
        ];
        $object = new stdClass();
        $context = $this->prophesize(SerializationContext::class)->reveal();

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata
            ->getRelations()
            ->willReturn($relations)
            ->shouldBeCalledTimes(1)
        ;

        $metadataFactory = $this->prophesize(MetadataFactoryInterface::class);
        $metadataFactory
            ->getMetadataForClass($object::class)
            ->willReturn($metadata)
            ->shouldBeCalledTimes(1)
        ;

        $ctx = [
            'object' => $object,
            'context' => $context,
        ];

        $ELProphecy = $this->prophesize(ExpressionEvaluatorInterface::class);
        $ELProphecy->evaluate('object.getFriend()', $ctx)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate('object.getManager()', $ctx)->willReturn(42)->shouldBeCalledTimes(1);
        $ELProphecy->evaluate('object.getXmlElementName()', $ctx)->willReturn('foo')->shouldBeCalledTimes(1);
        $ELProphecy->evaluate(Argument::any(), $ctx)->willReturnArgument();

        $exclusionManagerProphecy = $this->prophesize('Hateoas\Serializer\ExclusionManager');
        $exclusionManagerProphecy->shouldSkipEmbedded($object, $relations[0], $context)->willReturn(true);
        $exclusionManagerProphecy->shouldSkipEmbedded($object, $relations[1], $context)->willReturn(false);
        $exclusionManagerProphecy->shouldSkipEmbedded($object, $relations[2], $context)->willReturn(false);

        $embeddedsFactory = new EmbeddedsFactory(
            $metadataFactory->reveal(),
            $ELProphecy->reveal(),
            $exclusionManagerProphecy->reveal()
        );

        $embeddeds = $embeddedsFactory->create($object, $context);

        $this->assertCount(2, $embeddeds);
        $this->assertInstanceOf('Hateoas\Model\Embedded', $embeddeds[0]);
        $this->assertSame('friend', $embeddeds[0]->getRel());
        $this->assertSame(42, $embeddeds[0]->getData());
        $this->assertInstanceOf('Hateoas\Model\Embedded', $embeddeds[1]);
        $this->assertSame('manager', $embeddeds[1]->getRel());
        $this->assertSame(42, $embeddeds[1]->getData());
        $this->assertSame('foo', $embeddeds[1]->getXmlElementName());
    }

    protected function expr(string $expr): \JMS\Serializer\Expression\Expression
    {
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());

        return $expressionEvaluator->parse($expr, ['object']);
    }
}
