<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Representation;

use ArrayIterator;
use Zuruuh\Hateoas\Representation\CollectionRepresentation;

/**
 * @internal
 *
 * @coversNothing
 */
class CollectionRepresentationTest extends RepresentationTestCase
{
    /**
     * @dataProvider getTestSerializeData
     */
    public function testSerialize(array|ArrayIterator $resources): void
    {
        $collection = new CollectionRepresentation($resources);

        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <collection>
                  <entry rel="items">
                    <entry><![CDATA[Adrien]]></entry>
                    <entry><![CDATA[William]]></entry>
                  </entry>
                </collection>

                XML,
            $this->hateoas->serialize($collection, 'xml')
        );
        $this->assertSame(
            <<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <collection>
                  <resource rel="items"><![CDATA[Adrien]]></resource>
                  <resource rel="items"><![CDATA[William]]></resource>
                </collection>

                XML,
            $this->halHateoas->serialize($collection, 'xml')
        );

        $this->assertSame(
            <<<'JSON'
                {
                    "_embedded": {
                        "items": [
                            "Adrien",
                            "William"
                        ]
                    }
                }
                JSON,
            $this->json($this->halHateoas->serialize($collection, 'json'))
        );
    }

    public function getTestSerializeData(): array
    {
        return [
            [
                [
                    'Adrien',
                    'William',
                ],
            ],
            [
                new ArrayIterator([
                    'Adrien',
                    'William',
                ]),
            ],
        ];
    }
}
