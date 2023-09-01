<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Unit\Normalizer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Zuruuh\Hateoas\Loader\AttributeMappingLoader;
use Zuruuh\Hateoas\Normalizer\HypermediaNormalizer;
use Zuruuh\Hateoas\Tests\Fixtures\AdrienBrault;

#[CoversClass(HypermediaNormalizer::class)]
#[Group('debug')]
final class HypermediaNormalizerTest extends TestCase
{
    public function testHypermediaNormalization(): void
    {
        dump($this->createNormalizer()->normalize(new AdrienBrault()));
    }

    private function createNormalizer(): HypermediaNormalizer
    {
        $metadataLoader = new AttributeMappingLoader(new AnnotationLoader());
        $classMetadataFactory = new ClassMetadataFactory($metadataLoader);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory);
        $objectNormalizer->setSerializer(new Serializer([new GetSetMethodNormalizer(), $objectNormalizer], [new JsonEncoder()]));

        return new HypermediaNormalizer($classMetadataFactory, $objectNormalizer);
    }
}
