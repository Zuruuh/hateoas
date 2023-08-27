<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Annotation;

/**
 * @Annotation
 *
 * @Target("CLASS")
 */
final class Relation
{
    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $href;

    /**
     * @var mixed
     */
    public $embedded;

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * phpcs:disable.
     *
     * @var \Hateoas\Configuration\Annotation\Exclusion
     *                                                  phpcs:enable
     */
    public $exclusion;
}
