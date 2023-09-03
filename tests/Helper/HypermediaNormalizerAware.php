<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Helper;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Zuruuh\Hateoas\Expression\ExpressionEvaluator;
use Zuruuh\Hateoas\Loader\AttributeMappingLoader;
use Zuruuh\Hateoas\Normalizer\HypermediaNormalizer;

trait HypermediaNormalizerAware
{
    private function provideHypermediaNormalizer(): HypermediaNormalizer
    {
        $metadataLoader = new AttributeMappingLoader(new AnnotationLoader());
        $classMetadataFactory = new ClassMetadataFactory($metadataLoader);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory);
        $objectNormalizer->setSerializer(new Serializer([new GetSetMethodNormalizer(), $objectNormalizer], [new JsonEncoder()]));

        return new HypermediaNormalizer($classMetadataFactory, $objectNormalizer, new ExpressionEvaluator(new ExpressionLanguage()));
    }
}
