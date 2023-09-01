<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class JsonHalEncoder implements EncoderInterface
{
    public const FORMATS = [
        'json-hal',
        'json+hal',
        'hal+json',
        'application/hal+json',
    ];

    public function __construct(
        private readonly JsonEncode $jsonEncoder,
        private readonly ClassMetadataFactoryInterface $classMetadataFactory,
    ) {}

    public function encode(mixed $data, string $format, array $context = []): string
    {
        if (!is_array($data)) {
            // Maybe throw here ?
            return $this->jsonEncoder->encode($data, 'json');
        }

        $metadata = $this->classMetadataFactory->getMetadataFor($data);

        /* dump($metadata->getAttributesMetadata()); */

        return $this->jsonEncoder->encode($data, 'json');
    }

    public function supportsEncoding(string $format): bool
    {
        return in_array($format, self::FORMATS);
    }

    /* public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context): void */
    /* { */
    /*     $serializedLinks = []; */
    /*     foreach ($links as $link) { */
    /*         $serializedLink = array_merge([ */
    /*             'href' => $link->getHref(), */
    /*         ], $link->getAttributes()); */

    /*         if (!isset($serializedLinks[$link->getRel()]) && 'curies' !== $link->getRel()) { */
    /*             $serializedLinks[$link->getRel()] = $serializedLink; */
    /*         } elseif (isset($serializedLinks[$link->getRel()]['href'])) { */
    /*             $serializedLinks[$link->getRel()] = [ */
    /*                 $serializedLinks[$link->getRel()], */
    /*                 $serializedLink, */
    /*             ]; */
    /*         } else { */
    /*             $serializedLinks[$link->getRel()][] = $serializedLink; */
    /*         } */
    /*     } */

    /*     if (count($serializedLinks)) { */
    /*         $visitor->visitProperty(new StaticPropertyMetadata(self::class, '_links', $serializedLinks), $serializedLinks); */
    /*     } else { */
    /*         $visitor->visitProperty(new StaticPropertyMetadata(self::class, '_links', new \ArrayObject()), new \ArrayObject()); */
    /*     } */
    /* } */

    /*
     * @param Embedded [] $embeddeds
     */
    /* public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context): void */
    /* { */
    /*     $serializedEmbeddeds = []; */
    /*     $multiple = []; */
    /*     $navigator = $context->getNavigator(); */

    /*     foreach ($embeddeds as $embedded) { */
    /*         $context->pushPropertyMetadata($embedded->getMetadata()); */
    /*         try { */
    /*             $data = $navigator->accept($embedded->getData(), $embedded->getType(), $context); */

    /*             if (!isset($serializedEmbeddeds[$embedded->getRel()])) { */
    /*                 $serializedEmbeddeds[$embedded->getRel()] = $data; */
    /*             } elseif (!isset($multiple[$embedded->getRel()])) { */
    /*                 $multiple[$embedded->getRel()] = true; */

    /*                 $serializedEmbeddeds[$embedded->getRel()] = [$serializedEmbeddeds[$embedded->getRel()], $data]; */
    /*             } else { */
    /*                 $serializedEmbeddeds[$embedded->getRel()][] = $data; */
    /*             } */
    /*         } catch (NotAcceptableException $e) { */
    /*         } */

    /*         $context->popPropertyMetadata(); */
    /*     } */

    /*     if (count($serializedEmbeddeds)) { */
    /*         $visitor->visitProperty(new StaticPropertyMetadata(self::class, '_embedded', $serializedEmbeddeds), $serializedEmbeddeds); */
    /*     } else { */
    /*         $visitor->visitProperty(new StaticPropertyMetadata(self::class, '_embedded', new \ArrayObject()), new \ArrayObject()); */
    /*     } */
    /* } */
}
