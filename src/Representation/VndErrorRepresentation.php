<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Representation;

use JMS\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("resource")
 *
 * @Hateoas\Relation(
 *      "help",
 *       href = "expr(object.getHelp())",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getHelp() === null)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "describes",
 *      href = "expr(object.getDescribes())",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getDescribes() === null)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "about",
 *      href = "expr(object.getAbout())",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getAbout() === null)"
 *      )
 * )
 */
class VndErrorRepresentation
{
    public function __construct(
        /**
         * @Serializer\Expose
         * @Serializer\Type("string")
         */
        private readonly string $message,
        /**
         * @Serializer\Expose
         * @Serializer\XmlAttribute
         * @Serializer\Type("int")
         */
        private readonly ?int $logref = null,
        private readonly ?string $help = null,
        private readonly ?string $describes = null,
        private readonly ?string $about = null
    ) {}

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getDescribes(): ?string
    {
        return $this->describes;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLogref(): ?int
    {
        return $this->logref;
    }
}
