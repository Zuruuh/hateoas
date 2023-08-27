<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Zuruuh\Hateoas\HateoasBuilder;
use Zuruuh\Hateoas\Tests\Fixtures\AdrienBrault;
use Zuruuh\Hateoas\Tests\Fixtures\CircularReference1;
use Zuruuh\Hateoas\Tests\Fixtures\CircularReference2;
use Zuruuh\Hateoas\Tests\Fixtures\NoAnnotations;
use Zuruuh\Hateoas\Tests\Fixtures\WithAlternativeRouter;
use Zuruuh\Hateoas\UrlGenerator\CallableUrlGenerator;

/**
 * Contains functional tests.
 *
 * @internal
 *
 * @coversNothing
 */
class HateoasBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $hateoasBuilder = new HateoasBuilder();
        $hateoas = $hateoasBuilder->build();

        $this->assertInstanceOf(SerializerInterface::class, $hateoas);
    }

    public function testSerializeAdrienBraultWithExclusion(): void
    {
        $hateoas = HateoasBuilder::buildHateoas();

        $adrienBrault = new AdrienBrault();
        $fakeAdrienBrault = new AdrienBrault();
        $fakeAdrienBrault->firstName = 'John';
        $fakeAdrienBrault->lastName = 'Smith';

        $context = SerializationContext::create()->setGroups(['simple']);
        $context2 = clone $context;

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <result>
                  <first_name><![CDATA[Adrien]]></first_name>
                  <last_name><![CDATA[Brault]]></last_name>
                  <link rel="self" href="http://adrienbrault.fr"/>
                  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
                </result>

                XML,
            $hateoas->serialize($adrienBrault, 'xml', $context)
        );
        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <result>
                  <first_name><![CDATA[John]]></first_name>
                  <last_name><![CDATA[Smith]]></last_name>
                  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
                </result>

                XML,
            $hateoas->serialize($fakeAdrienBrault, 'xml', $context2)
        );
    }

    public function testAlternativeUrlGenerator(): void
    {
        $brokenUrlGenerator = new CallableUrlGenerator(fn (string $name, $parameters): string => $name.'?'.http_build_query($parameters));

        $hateoas = HateoasBuilder::create()
            ->setUrlGenerator('my_generator', $brokenUrlGenerator)
            ->build()
        ;

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <result>
                  <link rel="search" href="/search?query=hello"/>
                </result>

                XML,
            $hateoas->serialize(new WithAlternativeRouter(), 'xml')
        );
    }

    public function testCyclicalReferences(): void
    {
        $hateoas = HateoasBuilder::create()->build();

        $reference1 = new CircularReference1();
        $reference2 = new CircularReference2();
        $reference1->setReference2($reference2);
        $reference2->setReference1($reference1);

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <result>
                  <name><![CDATA[reference1]]></name>
                  <entry rel="reference2">
                    <name><![CDATA[reference2]]></name>
                    <entry rel="reference1"/>
                  </entry>
                </result>

                XML,
            $hateoas->serialize($reference1, 'xml')
        );

        $this->assertSame(
            '{"name":"reference1","_embedded":{"reference2":{"name":"reference2","_embedded":{}}}}',
            $hateoas->serialize($reference1, 'json')
        );
    }

    public function testWithNullInEmbedded(): void
    {
        $hateoas = HateoasBuilder::create()->build();

        $reference1 = new CircularReference1();

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <result xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                  <name><![CDATA[reference1]]></name>
                  <entry rel="reference2" xsi:nil="true"/>
                </result>

                XML,
            $hateoas->serialize($reference1, 'xml', SerializationContext::create()->setSerializeNull(true))
        );

        $this->assertSame(
            '{"name":"reference1","_embedded":{"reference2":null}}',
            $hateoas->serialize($reference1, 'json', SerializationContext::create()->setSerializeNull(true))
        );
    }

    public function testWithXmlRootNameFromXmlConfiguration(): void
    {
        $hateoas = HateoasBuilder::create()
            ->addMetadataDir(self::rootPath().'/Fixtures/config')
            ->build()
        ;

        $resource = new NoAnnotations('#303', 303);

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <resource>
                  <id><![CDATA[id-#303]]></id>
                  <number>303</number>
                  <link rel="self" href="https://github.com/willdurand/Hateoas/issues/303"/>
                </resource>

                XML,
            $hateoas->serialize($resource, 'xml')
        );
    }
}
