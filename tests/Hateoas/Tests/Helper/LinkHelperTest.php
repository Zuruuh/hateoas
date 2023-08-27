<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Helper;

use Metadata\MetadataFactoryInterface;
use PHPUnit_Framework_MockObject_Matcher_InvokedCount;
use RuntimeException;
use Zuruuh\Hateoas\Configuration\Metadata\ClassMetadata;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Factory\LinkFactory;
use Zuruuh\Hateoas\HateoasBuilder;
use Zuruuh\Hateoas\Helper\LinkHelper;
use Zuruuh\Hateoas\Model\Link;
use Zuruuh\Hateoas\Tests\Fixtures\Will;
use Zuruuh\Hateoas\Tests\TestCase;
use Zuruuh\Hateoas\UrlGenerator\CallableUrlGenerator;

/**
 * @internal
 *
 * @coversNothing
 */
class LinkHelperTest extends TestCase
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

    public function testGetLinkHref(): void
    {
        $linkHelper = new LinkHelper($this->getLinkFactoryMock(), $this->getMetadataFactoryMock());

        $this->assertEquals(
            'http://example.com/self',
            $linkHelper->getLinkHref(new Will(123), 'self')
        );
    }

    public function testGetLinkHrefReturnsNullIfRelNotFound(): void
    {
        $linkHelper = new LinkHelper($this->getLinkFactoryMock($this->never()), $this->getMetadataFactoryMock());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Can not find the relation "unknown-rel" for the "Hateoas\Tests\Fixtures\Will" class');

        $linkHelper->getLinkHref(new Will(123), 'unknown-rel');
    }

    /**
     * @return MetadataFactoryInterface
     */
    private function getMetadataFactoryMock()
    {
        $metadataMock = $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $metadataMock
            ->expects($this->once())
            ->method('getRelations')
            ->will($this->returnValue([
                new Relation('self', 'http://example.com/me'),
                new Relation('self-route', new Route('my-self-route')),
            ]))
        ;

        $metadataFactoryMock = $this->getMockBuilder(MetadataFactoryInterface::class)
            ->getMock()
        ;

        $metadataFactoryMock
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->will($this->returnValue($metadataMock))
        ;

        return $metadataFactoryMock;
    }

    /**
     * @param PHPUnit_Framework_MockObject_Matcher_InvokedCount $expects
     *
     * @return LinkFactory
     */
    private function getLinkFactoryMock($expects = null)
    {
        if (!$expects instanceof PHPUnit_Framework_MockObject_Matcher_InvokedCount) {
            $expects = $this->once();
        }

        $linkFactoryMock = $this->getMockBuilder('Hateoas\Factory\LinkFactory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $linkFactoryMock
            ->expects($expects)
            ->method('createLink')
            ->will($this->returnCallback(fn ($obj, Relation $relation): \Zuruuh\Hateoas\Model\Link => new Link($relation->getName(), 'http://example.com/'.$relation->getName())))
        ;

        return $linkFactoryMock;
    }
}
