<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Metadata\Driver;

use Zuruuh\Hateoas\Configuration\Embedded;
use Zuruuh\Hateoas\Configuration\Exclusion;
use Zuruuh\Hateoas\Configuration\Metadata\ClassMetadata;
use Zuruuh\Hateoas\Configuration\Provider\RelationProviderInterface;
use Zuruuh\Hateoas\Configuration\Relation;
use Zuruuh\Hateoas\Configuration\RelationProvider;
use Zuruuh\Hateoas\Configuration\Route;
use JMS\Serializer\Exception\XmlErrorException;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Type\ParserInterface;
use Metadata\ClassMetadata as JMSClassMetadata;
use Metadata\Driver\AbstractFileDriver;
use Metadata\Driver\FileLocatorInterface;

class XmlDriver extends AbstractFileDriver
{
    use CheckExpressionTrait;

    final public const NAMESPACE_URI = 'https://github.com/willdurand/Hateoas';

    public function __construct(
        FileLocatorInterface $locator,
        CompilableExpressionEvaluatorInterface $expressionLanguage,
        private readonly \Zuruuh\Hateoas\Configuration\Provider\RelationProviderInterface $relationProvider,
        private readonly \JMS\Serializer\Type\ParserInterface $typeParser
    ) {
        parent::__construct($locator);
        $this->expressionLanguage = $expressionLanguage;
    }

    protected function loadMetadataFromFile(\ReflectionClass $class, string $file): ?JMSClassMetadata
    {
        $previous = libxml_use_internal_errors(true);
        $root     = simplexml_load_file($file);
        libxml_use_internal_errors($previous);

        if (false === $root) {
            throw new XmlErrorException(libxml_get_last_error());
        }

        $name = $class->getName();
        if (!$exists = $root->xpath("./class[@name = '" . $name . "']")) {
            throw new \RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $name, $file));
        }

        $classMetadata = new ClassMetadata($name);
        $classMetadata->fileResources[] = $file;
        $classMetadata->fileResources[] = $class->getFileName();

        if ($exists[0]->attributes(self::NAMESPACE_URI)->providers instanceof \SimpleXMLElement) {
            $providers = preg_split('/\s*,\s*/', (string) $exists[0]->attributes(self::NAMESPACE_URI)->providers);

            foreach ($providers as $relationProvider) {
                $relations = $this->relationProvider->getRelations(new RelationProvider((string) $this->checkExpression($relationProvider)), $class->getName());
                foreach ($relations as $relation) {
                    $classMetadata->addRelation($relation);
                }
            }
        }

        $elements = $exists[0]->children(self::NAMESPACE_URI);

        foreach ($elements->relation as $relation) {
            $name = (string) $relation->attributes('')->rel;

            $href = null;
            if (property_exists($relation, 'href') && $relation->href instanceof \SimpleXMLElement) {
                $href = $this->createHref($relation->href, $name);
            }

            $embedded = null;
            if (property_exists($relation, 'embedded') && $relation->embedded instanceof \SimpleXMLElement) {
                $embedded = $this->createEmbedded($relation->embedded);
            }

            $attributes = [];
            foreach ($relation->attribute as $attribute) {
                $attributes[(string) $attribute->attributes('')->name] = $this->checkExpression((string) $attribute->attributes('')->value);
            }

            $exclusion = property_exists($relation, 'exclusion') && $relation->exclusion instanceof \SimpleXMLElement ? $this->parseExclusion($relation->exclusion) : null;

            $classMetadata->addRelation(
                new Relation(
                    $name,
                    $this->checkExpression($href),
                    $embedded,
                    $attributes,
                    $exclusion
                )
            );
        }

        return $classMetadata;
    }

    protected function getExtension(): string
    {
        return 'xml';
    }

    private function parseExclusion(\SimpleXMLElement $exclusion): Exclusion
    {
        return new Exclusion(
            property_exists($exclusion->attributes(''), 'groups') && $exclusion->attributes('')->groups instanceof \SimpleXMLElement ? preg_split('/\s*,\s*/', (string) $exclusion->attributes('')->groups) : null,
            isset($exclusion->attributes('')->{'since-version'}) ? (string) $exclusion->attributes('')->{'since-version'} : null,
            isset($exclusion->attributes('')->{'until-version'}) ? (string) $exclusion->attributes('')->{'until-version'} : null,
            isset($exclusion->attributes('')->{'max-depth'}) ? (int) $exclusion->attributes('')->{'max-depth'} : null,
            isset($exclusion->attributes('')->{'exclude-if'}) ? $this->checkExpression((string) $exclusion->attributes('')->{'exclude-if'}) : null
        );
    }

    /**
     * @param mixed $href
     * @param mixed $name
     *
     * @return mixed
     */
    private function createHref(\SimpleXMLElement $href, string $name)
    {
        if (property_exists($href->attributes(''), 'uri') && $href->attributes('')->uri instanceof \SimpleXMLElement && (property_exists($href->attributes(''), 'route') && $href->attributes('')->route instanceof \SimpleXMLElement)) {
            throw new \RuntimeException(sprintf(
                'uri and route attributes are mutually exclusive, please set only one of them. The problematic relation rel is %s.',
                $name
            ));
        } elseif (property_exists($href->attributes(''), 'uri') && $href->attributes('')->uri instanceof \SimpleXMLElement) {
            $href = $this->checkExpression((string) $href->attributes('')->uri);
        } else {
            $parameters = [];
            foreach ($href->parameter as $parameter) {
                $parameters[(string) $parameter->attributes('')->name] = $this->checkExpression((string) $parameter->attributes('')->value);
            }

            $absolute = false;
            if ($href->attributes('')->absolute instanceof \SimpleXMLElement) {
                $absolute = (string) $href->attributes('')->absolute;
                if ('true' === strtolower($absolute) || 'false' === strtolower($absolute)) {
                    $absolute = 'true' === strtolower($absolute);
                } else {
                    $absolute = $this->checkExpression($absolute);
                }
            }

            return new Route(
                $this->checkExpression((string) $href->attributes('')->route),
                $parameters,
                $absolute,
                property_exists($href->attributes(''), 'generator') && $href->attributes('')->generator instanceof \SimpleXMLElement ? (string) $href->attributes('')->generator : null
            );
        }

        return $this->checkExpression($href);
    }

    /**
     * @param mixed $embedded
     */
    private function createEmbedded(\SimpleXMLElement $embedded): Embedded
    {
        $embeddedExclusion = property_exists($embedded, 'exclusion') && $embedded->exclusion instanceof \SimpleXMLElement ? $this->parseExclusion($embedded->exclusion) : null;
        $xmlElementName = isset($embedded->attributes('')->{'xml-element-name'}) ? $this->checkExpression((string) $embedded->attributes('')->{'xml-element-name'}) : null;
        $type = isset($embedded->attributes('')->{'type'}) ? $this->typeParser->parse((string) $embedded->attributes('')->{'type'}) : null;

        return new Embedded(
            $this->checkExpression((string) $embedded->content),
            $xmlElementName,
            $embeddedExclusion,
            $type
        );
    }
}
