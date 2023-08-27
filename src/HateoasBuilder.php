<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;
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
use Zuruuh\Hateoas\Resolver\ExpressionLanguageResolver;
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
    private ?ExpressionLanguage $expressionLanguage = null;

    /**
     * @var array<string, mixed> $contextVariables
     */
    private array $contextVariables = [];

    private readonly UrlGeneratorRegistry $urlGeneratorRegistry;

    /**
     * @var list<ConfigurationExtensionInterface> $configurationExtensions
     */
    private array $configurationExtensions = [];

    /**
     * @var array<string, string> $metadataDirs
     */
    private array $metadataDirs = [];

    private readonly ChainProvider $chainProvider;

    private bool $debug = false;

    private bool $includeInterfaceMetadata = false;

    public function __construct(
        private readonly SymfonySerializerInterface $serializer,
        private readonly ClassMetadataFactoryInterface $classMetadataFactory,
    ) {
        $this->urlGeneratorRegistry = new UrlGeneratorRegistry();
        $this->chainProvider = new ChainProvider([
            new FunctionProvider(),
            new StaticMethodProvider(),
        ]);
    }

    public function build(): HateoasSerializer
    {
        $linkFactory = new LinkFactory($this->urlGeneratorRegistry, new ExpressionLanguageResolver());

        $this->contextVariables['link_helper'] = $linkHelper = new LinkHelper($linkFactory, $this->classMetadataFactory);
        $this->chainProvider->addProvider(new ExpressionEvaluatorProvider());

        $exclusionManager = new ExclusionManager();

        $linksFactory = new LinksFactory($metadataFactory, $linkFactory, $exclusionManager);
        $embeddedsFactory = new EmbeddedsFactory($metadataFactory, $expressionEvaluator, $exclusionManager);

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
            })
        ;

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
     * @param mixed $value
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
            throw new InvalidArgumentException(sprintf('The cache directory "%s" is not writable.', $dir));
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
     */
    public function setMetadataDirs(array $namespacePrefixToDirMap): HateoasBuilder
    {
        foreach ($namespacePrefixToDirMap as $dir) {
            if (!is_dir($dir)) {
                throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
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
     * @param string $dir             the directory where metadata files are located
     * @param string $namespacePrefix an optional prefix if you only store metadata for specific namespaces in this directory
     */
    public function addMetadataDir(string $dir, string $namespacePrefix = ''): HateoasBuilder
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        if (isset($this->metadataDirs[$namespacePrefix])) {
            throw new InvalidArgumentException(sprintf('There is already a directory configured for the namespace prefix "%s". Please use replaceMetadataDir() to override directories.', $namespacePrefix));
        }

        $this->metadataDirs[$namespacePrefix] = $dir;

        return $this;
    }

    /**
     * Add a map of namespace prefixes to directories.
     */
    public function addMetadataDirs(array $namespacePrefixToDirMap): HateoasBuilder
    {
        foreach ($namespacePrefixToDirMap as $prefix => $dir) {
            $this->addMetadataDir($dir, $prefix);
        }

        return $this;
    }

    /*
    private function buildMetadataFactory(): MetadataFactoryInterface
    {
        $annotationReader = $this->annotationReader;

        if (!$annotationReader instanceof \Doctrine\Common\Annotations\AnnotationReader) {
            $annotationReader = new AnnotationReader();

            if (null !== $this->cacheDir) {
                $this->createDir($this->cacheDir.'/annotations');
                $annotationReader = new FileCacheReader($annotationReader, $this->cacheDir.'/annotations', $this->debug);
            }
        }

        $expressionEvaluator = $this->getExpressionEvaluator();

        $typeParser = new Parser();

        if ([] !== $this->metadataDirs) {
            $fileLocator = new FileLocator($this->metadataDirs);
            $metadataDriver = new DriverChain([
                new YamlDriver($fileLocator, $expressionEvaluator, $this->chainProvider, $typeParser),
                new XmlDriver($fileLocator, $expressionEvaluator, $this->chainProvider, $typeParser),
                new AttributesDriver($annotationReader, $expressionEvaluator, $this->chainProvider, $typeParser),
            ]);
        } else {
            $metadataDriver = new AttributesDriver($annotationReader, $expressionEvaluator, $this->chainProvider, $typeParser);
        }

        $metadataDriver = new ExtensionDriver($metadataDriver, $this->configurationExtensions);
        $metadataFactory = new MetadataFactory($metadataDriver, null, $this->debug);
        $metadataFactory->setIncludeInterfaces($this->includeInterfaceMetadata);

        if (null !== $this->cacheDir) {
            $this->createDir($this->cacheDir.'/metadata');
            $metadataFactory->setCache(new FileCache($this->cacheDir.'/metadata'));
        }

        return $metadataFactory;
    }
    */

    private function getExpressionLanguage(): ExpressionLanguage
    {
        if (!$this->expressionLanguage instanceof ExpressionLanguage) {
            $this->expressionLanguage = new ExpressionLanguage();
            $this->expressionLanguage->registerProvider(new LinkExpressionFunction());
        }

        return $this->expressionLanguage;
    }
}
