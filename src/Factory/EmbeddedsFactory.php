<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Factory;

use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;
use Zuruuh\Hateoas\Model\Embedded;
use Zuruuh\Hateoas\Serializer\ExclusionManager;
use Zuruuh\Hateoas\Serializer\Metadata\RelationPropertyMetadata;

class EmbeddedsFactory
{
    public function __construct(private readonly MetadataFactoryInterface $metadataFactory, private readonly ExpressionEvaluatorInterface $expressionEvaluator, private readonly ExclusionManager $exclusionManager) {}

    /**
     * @return Embedded[]
     */
    public function create(object $object, SerializationContext $context): array
    {
        $embeddeds = [];

        if (null !== ($classMetadata = $this->metadataFactory->getMetadataForClass($object::class))) {
            $langugeData = ['object' => $object, 'context' => $context];

            /**
             * @var Relation $relation
             */
            foreach ($classMetadata->getRelations() as $relation) {
                if ($this->exclusionManager->shouldSkipEmbedded($object, $relation, $context)) {
                    continue;
                }

                $rel = $relation->getName();
                $data = $this->checkExpression($relation->getEmbedded()->getContent(), $langugeData);
                $xmlElementName = $this->checkExpression($relation->getEmbedded()->getXmlElementName(), $langugeData);

                $propertyMetadata = new RelationPropertyMetadata($relation->getEmbedded()->getExclusion(), $relation);

                $embeddeds[] = new Embedded($rel, $data, $propertyMetadata, $xmlElementName, $relation->getEmbedded()->getType());
            }
        }

        return $embeddeds;
    }

    /**
     * @param mixed $exp
     *
     * @return mixed
     */
    private function checkExpression($exp, array $data)
    {
        if ($exp instanceof Expression) {
            return $this->expressionEvaluator->evaluate((string) $exp, $data);
        }

        return $exp;
    }
}
