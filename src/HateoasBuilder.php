<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Type\Parser;
use Metadata\Cache\FileCache;
use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Zuruuh\Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Zuruuh\Hateoas\Configuration\Metadata\Driver\AttributesDriver;
use Zuruuh\Hateoas\Configuration\Metadata\Driver\ExtensionDriver;
use Zuruuh\Hateoas\Configuration\Metadata\Driver\XmlDriver;
use Zuruuh\Hateoas\Configuration\Metadata\Driver\YamlDriver;
use Zuruuh\Hateoas\Configuration\Provider\ChainProvider;
use Zuruuh\Hateoas\Configuration\Provider\ExpressionEvaluatorProvider;
use Zuruuh\Hateoas\Configuration\Provider\FunctionProvider;
use Zuruuh\Hateoas\Configuration\Provider\StaticMethodProvider;
use Zuruuh\Hateoas\Expression\LinkExpressionFunction;
use Zuruuh\Hateoas\Factory\EmbeddedsFactory;
use Zuruuh\Hateoas\Factory\LinkFactory;
use Zuruuh\Hateoas\Factory\LinksFactory;
use Zuruuh\Hateoas\Helper\LinkHelper;
use Zuruuh\Hateoas\Serializer\AddRelationsListener;
use Zuruuh\Hateoas\Serializer\ExclusionManager;
use Zuruuh\Hateoas\Serializer\JsonHalSerializer;
use Zuruuh\Hateoas\Serializer\Metadata\InlineDeferrer;
use Zuruuh\Hateoas\Serializer\SerializerInterface;
use Zuruuh\Hateoas\Serializer\XmlSerializer;
use Zuruuh\Hateoas\UrlGenerator\UrlGeneratorInterface;
use Zuruuh\Hateoas\UrlGenerator\UrlGeneratorRegistry;

class HateoasBuilder
{
    private readonly \JMS\Serializer\SerializerBuilder $serializerBuilder;

    private ?\Symfony\Component\ExpressionLanguage\ExpressionLanguage $expressionLanguage = null;

    private ?\JMS\Serializer\Expression\ExpressionEvaluator $expressionEvaluator = null;

    private array $contextVariables = [];

    private ?\Zuruuh\Hateoas\Serializer\SerializerInterface $xmlSerializer = null;

    private ?\Zuruuh\Hateoas\Serializer\SerializerInterface $jsonSerializer = null;

    private readonly \Zuruuh\Hateoas\UrlGenerator\UrlGeneratorRegistry $urlGeneratorRegistry;

    /**
     * @var ConfigurationExtensionInterface[]
     */
    private array $configurationExtensions = [];

    private readonly \Zuruuh\Hateoas\Configuration\Provider\ChainProvider $chainProvider;

    /**
     * @var string[]
     */
    private array $metadataDirs = [];

    private bool $debug = false;

    private ?string $cacheDir = null;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    private bool $includeInterfaceMetadata = false;

    public static function create(?SerializerBuilder $serializerBuilder = null): HateoasBuilder
    {
        return new static($serializerBuilder);
    }

    public static function buildHateoas(): Hateoas
    {
        $builder = static::create();

        return $builder->build();
    }

    public function __construct(?SerializerBuilder $serializerBuilder = null)
    {
        $this->serializerBuilder    = $serializerBuilder ?: SerializerBuilder::create();
        $this->urlGeneratorRegistry = new UrlGeneratorRegistry();
        $this->chainProvider        = new ChainProvider([
            new FunctionProvider(),
            new StaticMethodProvider(),
        ]);
    }

    /**
     * Build a configured Hateoas instance.
     */
    public function build(): Hateoas
    {
        $metadataFactory     = $this->buildMetadataFactory();

        $linkFactory         = new LinkFactory($this->urlGeneratorRegistry);
        $this->contextVariables['link_helper'] = $linkHelper = new LinkHelper($linkFactory, $metadataFactory);

        $expressionEvaluator =  $this->getExpressionEvaluator();
        foreach ($this->contextVariables as $name => $value) {
            $expressionEvaluator->setContextVariable($name, $value);
        }

        $this->chainProvider->addProvider(new ExpressionEvaluatorProvider($expressionEvaluator));

        $linkFactory->setExpressionEvaluator($expressionEvaluator);

        $exclusionManager    = new ExclusionManager(new ExpressionLanguageExclusionStrategy($expressionEvaluator));

        $linksFactory        = new LinksFactory($metadataFactory, $linkFactory, $exclusionManager);
        $embeddedsFactory    = new EmbeddedsFactory($metadataFactory, $expressionEvaluator, $exclusionManager);

        if (!$this->xmlSerializer instanceof \Zuruuh\Hateoas\Serializer\SerializerInterface) {
            $this->setDefaultXmlSerializer();
        }

        if (!$this->jsonSerializer instanceof \Zuruuh\Hateoas\Serializer\SerializerInterface) {
            $this->setDefaultJsonSerializer();
        }

        $eventListeners = [
            'xml' => new AddRelationsListener(
                $this->xmlSerializer,
                $linksFactory,
                $embeddedsFactory,
                new InlineDeferrer(),
                new InlineDeferrer()
            ),
            'json' => new AddRelationsListener(
                $this->jsonSerializer,
                $linksFactory,
                $embeddedsFactory,
                new InlineDeferrer(),
                new InlineDeferrer()
            ),
        ];

        $this->serializerBuilder
            ->addDefaultListeners()
            ->configureListeners(static function (EventDispatcherInterface $dispatcher) use ($eventListeners): void {
                foreach ($eventListeners as $format => $listener) {
                    $dispatcher->addListener(Events::POST_SERIALIZE, $listener->onPostSerialize(...), null, $format);
                }
            });

        $this->serializerBuilder->addMetadataDirs($this->metadataDirs);
        $this->serializerBuilder->setExpressionEvaluator($this->expressionEvaluator);

        $jmsSerializer = $this->serializerBuilder->build();

        return new Hateoas($jmsSerializer, $linkHelper);
    }

    public function setXmlSerializer(SerializerInterface $xmlSerializer): HateoasBuilder
    {
        $this->xmlSerializer = $xmlSerializer;

        return $this;
    }

    /**
     * Set the default XML serializer (`XmlSerializer`).
     */
    public function setDefaultXmlSerializer(): HateoasBuilder
    {
        return $this->setXmlSerializer(new XmlSerializer());
    }

    public function setJsonSerializer(SerializerInterface $jsonSerializer): HateoasBuilder
    {
        $this->jsonSerializer = $jsonSerializer;

        return $this;
    }

    /**
     * Set the default JSON serializer (`JsonHalSerializer`).
     */
    public function setDefaultJsonSerializer(): HateoasBuilder
    {
        return $this->setJsonSerializer(new JsonHalSerializer());
    }

    /**
     * Add a new URL generator. If you pass `null` as name, it will be the
     * default URL generator.
     */
    public function setUrlGenerator(?string $name, UrlGeneratorInterface $urlGenerator): HateoasBuilder
    {
        $this->urlGeneratorRegistry->set($name, $urlGenerator);

        return $this;
    }

    /**
     * Add a new expression context variable.
     *
     * @param mixed  $value
     */
    public function setExpressionContextVariable(string $name, $value): HateoasBuilder
    {
        $this->contextVariables[$name] = $value;

        return $this;
    }

    public function setExpressionLanguage(ExpressionLanguage $expressionLanguage): HateoasBuilder
    {
        $this->expressionLanguage = $expressionLanguage;

        return $this;
    }

    public function addConfigurationExtension(ConfigurationExtensionInterface $configurationExtension): HateoasBuilder
    {
        $this->configurationExtensions[] = $configurationExtension;

        return $this;
    }

    public function setDebug(bool $debug): HateoasBuilder
    {
        $this->debug = (bool) $debug;

        return $this;
    }

    public function setCacheDir(string $dir): HateoasBuilder
    {
        if (!is_dir($dir)) {
            $this->createDir($dir);
        }

        if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('The cache directory "%s" is not writable.', $dir));
        }

        $this->cacheDir = $dir;

        return $this;
    }

    /**
     * @param bool $include Whether to include the metadata from the interfaces
     */
    public function includeInterfaceMetadata(bool $include): HateoasBuilder
    {
        $this->includeInterfaceMetadata = (bool) $include;

        return $this;
    }

    /**
     * Set a map of namespace prefixes to directories.
     *
     * This method overrides any previously defined directories.
     *
     * @param array $namespacePrefixToDirMap
     */
    public function setMetadataDirs(array $namespacePrefixToDirMap): HateoasBuilder
    {
        foreach ($namespacePrefixToDirMap as $dir) {
            if (!is_dir($dir)) {
                throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
            }
        }

        $this->metadataDirs = $namespacePrefixToDirMap;

        return $this;
    }

    /**
     * Add a directory where the serializer will look for class metadata.
     *
     * The namespace prefix will make the names of the actual metadata files a bit shorter. For example, let's assume
     * that you have a directory where you only store metadata files for the ``MyApplication\Entity`` namespace.
     *
     * If you use an empty prefix, your metadata files would need to look like:
     *
     * ``my-dir/MyApplication.Entity.SomeObject.yml``
     * ``my-dir/MyApplication.Entity.OtherObject.yml``
     *
     * If you use ``MyApplication\Entity`` as prefix, your metadata files would need to look like:
     *
     * ``my-dir/SomeObject.yml``
     * ``my-dir/OtherObject.yml``
     *
     * Please keep in mind that you currently may only have one directory per namespace prefix.
     *
     * @param string $dir             The directory where metadata files are located.
     * @param string $namespacePrefix An optional prefix if you only store metadata for specific namespaces in this directory.
     */
    public function addMetadataDir(string $dir, string $namespacePrefix = ''): HateoasBuilder
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        if (isset($this->metadataDirs[$namespacePrefix])) {
            throw new \InvalidArgumentException(sprintf('There is already a directory configured for the namespace prefix "%s". Please use replaceMetadataDir() to override directories.', $namespacePrefix));
        }

        $this->metadataDirs[$namespacePrefix] = $dir;

        return $this;
    }

    /**
     * Add a map of namespace prefixes to directories.
     *
     * @param array $namespacePrefixToDirMap
     */
    public function addMetadataDirs(array $namespacePrefixToDirMap): HateoasBuilder
    {
        foreach ($namespacePrefixToDirMap as $prefix => $dir) {
            $this->addMetadataDir($dir, $prefix);
        }

        return $this;
    }

    /**
     * Similar to addMetadataDir(), but overrides an existing entry.
     */
    public function replaceMetadataDir(string $dir, string $namespacePrefix = ''): HateoasBuilder
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        if (!isset($this->metadataDirs[$namespacePrefix])) {
            throw new \InvalidArgumentException(sprintf('There is no directory configured for namespace prefix "%s". Please use addMetadataDir() for adding new directories.', $namespacePrefix));
        }

        $this->metadataDirs[$namespacePrefix] = $dir;

        return $this;
    }

    private function buildMetadataFactory(): MetadataFactoryInterface
    {
        $annotationReader = $this->annotationReader;

        if (!$annotationReader instanceof \Doctrine\Common\Annotations\AnnotationReader) {
            $annotationReader = new AnnotationReader();

            if (null !== $this->cacheDir) {
                $this->createDir($this->cacheDir . '/annotations');
                $annotationReader = new FileCacheReader($annotationReader, $this->cacheDir . '/annotations', $this->debug);
            }
        }

        $expressionEvaluator =  $this->getExpressionEvaluator();

        $typeParser = new Parser();

        if ($this->metadataDirs !== []) {
            $fileLocator    = new FileLocator($this->metadataDirs);
            $metadataDriver = new DriverChain([
                new YamlDriver($fileLocator, $expressionEvaluator, $this->chainProvider, $typeParser),
                new XmlDriver($fileLocator, $expressionEvaluator, $this->chainProvider, $typeParser),
                new AttributesDriver($annotationReader, $expressionEvaluator, $this->chainProvider, $typeParser),
            ]);
        } else {
            $metadataDriver = new AttributesDriver($annotationReader, $expressionEvaluator, $this->chainProvider, $typeParser);
        }

        $metadataDriver  = new ExtensionDriver($metadataDriver, $this->configurationExtensions);
        $metadataFactory = new MetadataFactory($metadataDriver, null, $this->debug);
        $metadataFactory->setIncludeInterfaces($this->includeInterfaceMetadata);

        if (null !== $this->cacheDir) {
            $this->createDir($this->cacheDir . '/metadata');
            $metadataFactory->setCache(new FileCache($this->cacheDir . '/metadata'));
        }

        return $metadataFactory;
    }

    private function createDir(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        if (!@mkdir($dir, 0o777, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }

    private function getExpressionLanguage(): ExpressionLanguage
    {
        if (!$this->expressionLanguage instanceof \Symfony\Component\ExpressionLanguage\ExpressionLanguage) {
            $this->expressionLanguage = new ExpressionLanguage();
            $this->expressionLanguage->registerProvider(new LinkExpressionFunction());
        }

        return $this->expressionLanguage;
    }

    private function getExpressionEvaluator(): ExpressionEvaluator
    {
        if (!$this->expressionEvaluator instanceof \JMS\Serializer\Expression\ExpressionEvaluator) {
            $this->expressionEvaluator = new ExpressionEvaluator($this->getExpressionLanguage(), $this->contextVariables);
        }

        return $this->expressionEvaluator;
    }
}
