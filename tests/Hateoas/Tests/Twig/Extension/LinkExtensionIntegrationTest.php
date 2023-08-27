<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Twig\Extension;

use Zuruuh\Hateoas\HateoasBuilder;
use Zuruuh\Hateoas\Twig\Extension\LinkExtension;
use Zuruuh\Hateoas\UrlGenerator\CallableUrlGenerator;
use Twig\Test\IntegrationTestCase;

class LinkExtensionIntegrationTest extends IntegrationTestCase
{
    public function getExtensions()
    {
        $hateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, new CallableUrlGenerator(function ($name, $parameters, $absolute) {
                return sprintf(
                    '%s/%s%s',
                    $absolute ? 'http://example.com' : '',
                    $name,
                    strtr('/id', $parameters)
                );
            }))
            ->build();

        return [
            new LinkExtension($hateoas->getLinkHelper()),
        ];
    }

    public function getFixturesDir()
    {
        return __DIR__ . '/../Fixtures/';
    }
}
