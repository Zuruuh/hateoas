<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Factory;

use RuntimeException;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Expression\Expression;
use Zuruuh\Hateoas\Model\Link;
use Zuruuh\Hateoas\Resolver\ExpressionLanguageResolver;
use Zuruuh\Hateoas\UrlGenerator\UrlGeneratorRegistry;

class LinkFactory implements LinkFactoryInterface
{
    public function __construct(
        private readonly UrlGeneratorRegistry $urlGeneratorRegistry,
        private readonly ExpressionLanguageResolver $expressionLanguage,
    ) {}

    public function createLink(object $object, Relation $relation): Link
    {
        $rel = $relation->name;
        $href = $relation->href;

        if ($href instanceof Route) {
            if (!$this->urlGeneratorRegistry->hasGenerators()) {
                throw new RuntimeException('You cannot use a route without an url generator.');
            }

            $name = $this->expressionLanguage->checkExpression($href->name, $object);
//            $parameters = is_array($href->parameters)
 //               ? $this->evaluateArray($href->parameters, $data)
   //             : $this->checkExpression($href->parameters, $data);
  //          $isAbsolute = $this->checkExpression($href->isAbsolute, $data);

            if (!is_array($href->parameters)) {
                throw new RuntimeException(
                    sprintf(
                        'The route parameters should be an array, %s given. Maybe you forgot to wrap the expression in expr(...).',
                        gettype($href->parameters)
                    )
                );
            }

            $href = $this->urlGeneratorRegistry
                ->get($href->generator)
                ->generate($name, $href->parameters, $href->isAbsolute)
            ;
        } else {
            // $href = $this->checkExpression($href, $data);
        }

        // $attributes = $this->evaluateArray($relation->getAttributes(), $data);

        return new Link($rel, $href, []);
    }

    /**
     * @param mixed $exp
     *
     * @return mixed
    private function checkExpression($exp, array $data)
    {
        if ($exp instanceof Expression) {
            return $this->expressionEvaluator->evaluateParsed($exp, $data);
        }

        return $exp;
    }

    private function evaluateArray(array $array, array $data): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $value = is_array($value) ? $this->evaluateArray($value, $data) : $this->checkExpression($value, $data);

            $newArray[$key] = $value;
        }

        return $newArray;
    }
**/
}
