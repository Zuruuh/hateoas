<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Annotation;

/**
 * @Annotation
 *
 * @Target("ANNOTATION")
 */
class Embedded
{
    /**
     * @Required
     *
     * @var mixed
     */
    public $content;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $xmlElementName;

    /**
     * phpcs:disable.
     *
     * @var \Hateoas\Configuration\Annotation\Exclusion
     *                                                  phpcs:enable
     */
    public $exclusion;
}
