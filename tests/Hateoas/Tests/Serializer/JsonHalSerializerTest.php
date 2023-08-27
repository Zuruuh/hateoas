<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Serializer;

use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Zuruuh\Hateoas\HateoasBuilder;
use Zuruuh\Hateoas\Model\Embedded;
use Zuruuh\Hateoas\Model\Link;
use Zuruuh\Hateoas\Representation\CollectionRepresentation;
use Zuruuh\Hateoas\Serializer\JsonHalSerializer;
use Zuruuh\Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use Zuruuh\Hateoas\Tests\Fixtures\AdrienBrault;
use Zuruuh\Hateoas\Tests\Fixtures\Foo1;
use Zuruuh\Hateoas\Tests\Fixtures\Foo2;
use Zuruuh\Hateoas\Tests\Fixtures\Foo3;
use Zuruuh\Hateoas\Tests\Fixtures\Gh236Foo;
use Zuruuh\Hateoas\Tests\Fixtures\LinkAttributes;
use Zuruuh\Hateoas\Tests\TestCase;

class JsonHalSerializerTest extends TestCase
{
    use ProphecyTrait;

    public function testSerializeLinks(): void
    {
        $links = [
            new Link('self', '/users/42', ['awesome' => 'exactly']),
            new Link('foo', '/bar'),
            new Link('foo', '/baz'),
            new Link('bar', '/foo'),
            new Link('bar', '/baz'),
            new Link('bar', '/buzz'),
        ];

        $expectedSerializedLinks = [
            'self' => [
                'href' => '/users/42',
                'awesome' => 'exactly',
            ],
            'foo' => [
                ['href' => '/bar'],
                ['href' => '/baz'],
            ],
            'bar' => [
                ['href' => '/foo'],
                ['href' => '/baz'],
                ['href' => '/buzz'],
            ],
        ];

        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $jsonSerializationVisitorProphecy = $this->prophesize(SerializationVisitorInterface::class);
        $jsonSerializationVisitorProphecy
            ->visitProperty(new StaticPropertyMetadata(JsonHalSerializer::class, '_links', $expectedSerializedLinks), $expectedSerializedLinks)
            ->shouldBeCalledTimes(1);

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeLinks(
            $links,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }

    public function testSerializeEmbeddeds(): void
    {
        $acceptArguments = [
            ['name' => 'John'],
            ['name' => 'Bar'],
            ['name' => 'Baz'],
            ['name' => 'Foo'],
            ['name' => 'Baz'],
            ['name' => 'Buzz'],
        ];

        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');
        $navigatorProphecy = $this->prophesize('JMS\Serializer\GraphNavigatorInterface');

        $contextProphecy
            ->getNavigator()
            ->willReturn($navigatorProphecy);

        foreach ($acceptArguments as $arg) {
            $navigatorProphecy
                ->accept($arg, null, $contextProphecy)
                ->willReturnArgument();
        }

        $contextProphecy->pushPropertyMetadata(Argument::type('Hateoas\Serializer\Metadata\RelationPropertyMetadata'))->shouldBeCalled();
        $contextProphecy->popPropertyMetadata()->shouldBeCalled();
        $embeddeds = [
            new Embedded('friend', ['name' => 'John'], new RelationPropertyMetadata()),
            new Embedded('foo', ['name' => 'Bar'], new RelationPropertyMetadata()),
            new Embedded('foo', ['name' => 'Baz'], new RelationPropertyMetadata()),
            new Embedded('bar', ['name' => 'Foo'], new RelationPropertyMetadata()),
            new Embedded('bar', ['name' => 'Baz'], new RelationPropertyMetadata()),
            new Embedded('bar', ['name' => 'Buzz'], new RelationPropertyMetadata()),
        ];

        $expectedEmbeddedded = [
            'friend' => ['name' => 'John'],
            'foo' => [
                ['name' => 'Bar'],
                ['name' => 'Baz'],
            ],
            'bar' => [
                ['name' => 'Foo'],
                ['name' => 'Baz'],
                ['name' => 'Buzz'],
            ],
        ];

        $jsonSerializationVisitorProphecy = $this->prophesize(SerializationVisitorInterface::class);
        $jsonSerializationVisitorProphecy
            ->visitProperty(new StaticPropertyMetadata(JsonHalSerializer::class, '_embedded', $expectedEmbeddedded), $expectedEmbeddedded)
            ->shouldBeCalledTimes(1);

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeEmbeddeds(
            $embeddeds,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }

    public function testSerializeCuriesWithOneLinkShouldBeAnArray(): void
    {
        $links = [
            new Link('self', '/users/42'),
            new Link('curies', '/rels/{rel}', ['name' => 'p']),
        ];

        $expectedSerializedLinks = [
            'self' => ['href' => '/users/42'],
            'curies' => [
                [
                    'href' => '/rels/{rel}',
                    'name' => 'p',
                ],
            ],
        ];

        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $jsonSerializationVisitorProphecy = $this->prophesize(SerializationVisitorInterface::class);
        $jsonSerializationVisitorProphecy
            ->visitProperty(new StaticPropertyMetadata(JsonHalSerializer::class, '_links', $expectedSerializedLinks), $expectedSerializedLinks)
            ->shouldBeCalledTimes(1);

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeLinks(
            $links,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }

    public function testSerializeCuriesWithMultipleEntriesShouldBeAnArray(): void
    {
        $links = [
            new Link('self', '/users/42'),
            new Link('curies', '/rels/{rel}', ['name' => 'p']),
            new Link('curies', '/foo/rels/{rel}', ['name' => 'foo']),
        ];

        $expectedSerializedLinks = [
            'self' => ['href' => '/users/42'],
            'curies' => [
                [
                    'href' => '/rels/{rel}',
                    'name' => 'p',
                ],
                [
                    'href' => '/foo/rels/{rel}',
                    'name' => 'foo',
                ],
            ],
        ];

        $contextProphecy = $this->prophesize('JMS\Serializer\SerializationContext');

        $jsonSerializationVisitorProphecy = $this->prophesize(SerializationVisitorInterface::class);
        $jsonSerializationVisitorProphecy
            ->visitProperty(new StaticPropertyMetadata(JsonHalSerializer::class, '_links', $expectedSerializedLinks), $expectedSerializedLinks)
            ->shouldBeCalledTimes(1);

        $jsonHalSerializer = new JsonHalSerializer();
        $jsonHalSerializer->serializeLinks(
            $links,
            $jsonSerializationVisitorProphecy->reveal(),
            $contextProphecy->reveal()
        );
    }

    public function testSerializeAdrienBrault(): void
    {
        $hateoas      = HateoasBuilder::buildHateoas();
        $adrienBrault = new AdrienBrault();

        $this->assertSame(
            <<<JSON
                {
                    "first_name": "Adrien",
                    "last_name": "Brault",
                    "_links": {
                        "self": {
                            "href": "http:\/\/adrienbrault.fr"
                        },
                        "computer": {
                            "href": "http:\/\/www.apple.com\/macbook-pro\/"
                        },
                        "dynamic-relation": {
                            "href": "awesome!!!"
                        }
                    },
                    "_embedded": {
                        "computer": {
                            "name": "MacBook Pro"
                        },
                        "broken-computer": {
                            "name": "Windows Computer"
                        },
                        "smartphone": [
                            {
                                "name": "iPhone 6"
                            },
                            {
                                "name": "Nexus 5"
                            }
                        ],
                        "dynamic-relation": [
                            "wowowow"
                        ]
                    }
                }
                JSON,
            $this->json($hateoas->serialize($adrienBrault, 'json'))
        );
    }

    public function testSerializeInlineJson(): void
    {
        $foo1 = new Foo1();
        $foo2 = new Foo2();
        $foo3 = new Foo3();
        $foo1->inline = $foo2;
        $foo2->inline = $foo3;

        $hateoas = HateoasBuilder::buildHateoas();

        $this->assertSame(
            <<<JSON
                {
                    "_links": {
                        "self3": {
                            "href": "foo3"
                        },
                        "self2": {
                            "href": "foo2"
                        },
                        "self1": {
                            "href": "foo1"
                        }
                    },
                    "_embedded": {
                        "self3": "foo3",
                        "self2": "foo2",
                        "self1": "foo1"
                    }
                }
                JSON,
            $this->json($hateoas->serialize($foo1, 'json'))
        );
    }

    public function testGh236(): void
    {
        $data = new CollectionRepresentation([new Gh236Foo()]);

        $hateoas = HateoasBuilder::buildHateoas();

        $this->assertSame(
            <<<JSON
                {
                    "_embedded": {
                        "items": [
                            {
                                "a": {
                                    "xxx": "yyy"
                                },
                                "_embedded": {
                                    "b_embed": {
                                        "xxx": "zzz"
                                    }
                                }
                            }
                        ]
                    }
                }
                JSON,
            $this->json(
                $hateoas->serialize($data, 'json', SerializationContext::create()->enableMaxDepthChecks())
            )
        );
    }

    public function testTemplateLink(): void
    {
        $data = new LinkAttributes();

        $hateoas = HateoasBuilder::create()
            ->addMetadataDir(__DIR__ . '/../Fixtures/config/')
            ->build();

        $this->assertSame(
            <<<JSON
                {
                    "_links": {
                        "self": {
                            "href": "https:\/\/github.com\/willdurand\/Hateoas\/issues\/305",
                            "templated": false
                        },
                        "foo": {
                            "href": "http:\/\/foo{?bar}",
                            "templated": true
                        },
                        "bar": {
                            "href": "http:\/\/foo\/bar",
                            "templated": false,
                            "number": 2
                        }
                    }
                }
                JSON,
            $this->json(
                $hateoas->serialize($data, 'json')
            )
        );
    }
}
