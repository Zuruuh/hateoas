<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Expression;

use stdClass;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Zuruuh\Hateoas\Expression\ExpressionEvaluator;
use Zuruuh\Hateoas\Expression\LinkExpressionFunction;
use Zuruuh\Hateoas\Helper\LinkHelper;
use Zuruuh\Hateoas\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class LinkExpressionFunctionTest extends TestCase
{
    public function testEvaluate(): void
    {
        $object = new stdClass();

        $linkHelperMock = $this->mockHelper('/foo', $object, 'self', false);

        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $expressionEvaluator->registerFunction(new LinkExpressionFunction($linkHelperMock));

        $this->assertEquals(
            '/foo',
            $expressionEvaluator->evaluate('expr(link(object, "self", false))', $object)
        );
    }

    public function testCompile(): void
    {
        $object = new stdClass();

        $linkHelperMock = $this->mockHelper('/foo', $object, 'self', false);

        $expressionLanguage = new ExpressionLanguage();
        $expressionEvaluator = new ExpressionEvaluator($expressionLanguage);
        $expressionEvaluator->registerFunction(new LinkExpressionFunction($linkHelperMock));

        $compiledExpression = $expressionLanguage->compile('link(object, "self", false)', ['object', 'link_helper']);

        // setup variables for expression eval
        $object = $object;
        $link_helper = $linkHelperMock;

        $this->assertEquals('/foo', eval(sprintf('return %s;', $compiledExpression)));
    }

    /**
     * @return LinkHelper
     */
    private function mockHelper(string $result, stdClass $expectedObject, string $expectedRel, bool $expectedAbsolute)
    {
        $linkHelperMock = $this
            ->getMockBuilder('Hateoas\Helper\LinkHelper')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $linkHelperMock
            ->expects($this->once())
            ->method('getLinkHref')
            ->will($this->returnValue('/foo'))
            ->with($expectedObject, $expectedRel, $expectedAbsolute)
        ;

        return $linkHelperMock;
    }
}
