<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Serializer;

use JMS\Serializer\SerializationContext;
use Zuruuh\Hateoas\HateoasBuilder;
use Zuruuh\Hateoas\Representation\CollectionRepresentation;
use Zuruuh\Hateoas\Serializer\XmlHalSerializer;
use Zuruuh\Hateoas\Tests\Fixtures\AdrienBrault;
use Zuruuh\Hateoas\Tests\Fixtures\Gh236Foo;
use Zuruuh\Hateoas\Tests\Fixtures\LinkAttributes;
use Zuruuh\Hateoas\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class XmlHalSerializerTest extends TestCase
{
    public function testSerializeAdrienBrault(): void
    {
        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->build()
        ;
        $adrienBrault = new AdrienBrault();

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <result href="http://adrienbrault.fr">
                  <first_name><![CDATA[Adrien]]></first_name>
                  <last_name><![CDATA[Brault]]></last_name>
                  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
                  <link rel="dynamic-relation" href="awesome!!!"/>
                  <resource rel="computer">
                    <name><![CDATA[MacBook Pro]]></name>
                  </resource>
                  <resource rel="broken-computer">
                    <name><![CDATA[Windows Computer]]></name>
                  </resource>
                  <resource rel="smartphone">
                    <name><![CDATA[iPhone 6]]></name>
                  </resource>
                  <resource rel="smartphone">
                    <name><![CDATA[Nexus 5]]></name>
                  </resource>
                  <resource rel="dynamic-relation"><![CDATA[wowowow]]></resource>
                </result>

                XML,
            $hateoas->serialize($adrienBrault, 'xml')
        );
    }

    public function testGh236(): void
    {
        $data = new CollectionRepresentation([new Gh236Foo()]);

        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->build()
        ;

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <collection>
                  <resource rel="items">
                    <a>
                      <xxx><![CDATA[yyy]]></xxx>
                    </a>
                    <resource rel="b_embed">
                      <xxx><![CDATA[zzz]]></xxx>
                    </resource>
                  </resource>
                </collection>

                XML,
            $hateoas->serialize($data, 'xml', SerializationContext::create()->enableMaxDepthChecks())
        );
    }

    public function testTemplateLink(): void
    {
        $data = new LinkAttributes();

        $hateoas = HateoasBuilder::create()
            ->setXmlSerializer(new XmlHalSerializer())
            ->addMetadataDir(__DIR__.'/../Fixtures/config/')
            ->build()
        ;

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <result templated="false" href="https://github.com/willdurand/Hateoas/issues/305">
                  <link rel="foo" href="http://foo{?bar}" templated="true"/>
                  <link rel="bar" href="http://foo/bar" templated="false" number="2"/>
                </result>

                XML,
            $hateoas->serialize($data, 'xml')
        );
    }
}
