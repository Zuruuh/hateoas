<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Representation;

use Zuruuh\Hateoas\Hateoas;
use Zuruuh\Hateoas\HateoasBuilder;
use Zuruuh\Hateoas\Serializer\XmlHalSerializer;
use Zuruuh\Hateoas\Tests\TestCase;
use Zuruuh\Hateoas\UrlGenerator\CallableUrlGenerator;

abstract class RepresentationTestCase extends TestCase
{
    /**
     * @var Hateoas
     */
    protected $hateoas;

    /**
     * @var Hateoas
     */
    protected $halHateoas;

    private \Zuruuh\Hateoas\UrlGenerator\CallableUrlGenerator $queryStringUrlGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryStringUrlGenerator = new CallableUrlGenerator(function (string $route, array $parameters, $absolute): string {
            if ('' !== $queryString = http_build_query($parameters)) {
                $queryString = '?' . $queryString;
            }

            return ($absolute ? 'http://example.com' : '') . $route . $queryString;
        });

        $this->hateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, $this->queryStringUrlGenerator)
            ->build();

        $this->halHateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, $this->queryStringUrlGenerator)
            ->setXmlSerializer(new XmlHalSerializer())
            ->build();
    }
}
