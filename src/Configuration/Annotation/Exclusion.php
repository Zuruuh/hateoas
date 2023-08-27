<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Configuration\Annotation;

/**
 * @Annotation
 *
 * @Target("ANNOTATION")
 */
final class Exclusion
{
    /**
     * @var array
     */
    public $groups;

    /**
     * @var string
     */
    public $sinceVersion;

    /**
     * @var string
     */
    public $untilVersion;

    /**
     * If you are working with the FOS/Rest Bundle, be reminded to set
     * Rest\View(serializerEnableMaxDepthChecks=true)
     * on your controller.
     *
     * @var int
     */
    public $maxDepth;

    /**
     * @var string
     */
    public $excludeIf;
}
