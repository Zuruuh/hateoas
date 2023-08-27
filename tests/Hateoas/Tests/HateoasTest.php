<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests;

use RuntimeException;
use Zuruuh\Hateoas\HateoasBuilder;
use Zuruuh\Hateoas\Tests\Fixtures\Will;
use Zuruuh\Hateoas\UrlGenerator\CallableUrlGenerator;

/**
 * @internal
 *
 * @coversNothing
 */
class HateoasTest extends TestCase
{
    private \Zuruuh\Hateoas\Hateoas $hateoas;

    protected function setUp(): void
    {
        $this->hateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, new CallableUrlGenerator(function ($name, $parameters, $absolute): string {
                if ('user_get' === $name) {
                    return sprintf(
                        '%s%s',
                        $absolute ? 'http://example.com' : '',
                        strtr('/users/id', $parameters)
                    );
                }

                if ('post_get' === $name) {
                    return sprintf(
                        '%s%s',
                        $absolute ? 'http://example.com' : '',
                        strtr('/posts/id', $parameters)
                    );
                }

                throw new RuntimeException('Cannot generate URL');
            }))
            ->build()
        ;
    }

    public function testGetLinkHrefUrlWithUnknownRelThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Can not find the relation "unknown-rel" for the "Hateoas\Tests\Fixtures\Will" class');
        $this->assertNull($this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'unknown-rel'));
        $this->assertNull($this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'unknown-rel', true));
    }

    public function testGetLinkHrefUrl(): void
    {
        $this->assertEquals('/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'self'));
        $this->assertEquals('/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'self', false));
    }

    public function testGetLinkHrefUrlWithAbsoluteTrue(): void
    {
        $this->assertEquals('http://example.com/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'self', true));
    }
}
