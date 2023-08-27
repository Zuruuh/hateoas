<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Representation\Factory;

use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Representation\CollectionRepresentation;
use Zuruuh\Hateoas\Representation\PaginatedRepresentation;
use Pagerfanta\Pagerfanta;

class PagerfantaFactory
{
    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    public function __construct(?string $pageParameterName = null, ?string $limitParameterName = null)
    {
        $this->pageParameterName  = $pageParameterName;
        $this->limitParameterName = $limitParameterName;
    }

    /**
     * @param Pagerfanta $pager  The pager
     * @param Route      $route  The collection's route
     * @param mixed      $inline Most of the time, a custom `CollectionRepresentation` instance
     */
    public function createRepresentation(Pagerfanta $pager, Route $route, $inline = null): PaginatedRepresentation
    {
        if (null === $inline) {
            $inline = new CollectionRepresentation($pager->getCurrentPageResults());
        }

        return new PaginatedRepresentation(
            $inline,
            $route->getName(),
            $route->getParameters(),
            $pager->getCurrentPage(),
            $pager->getMaxPerPage(),
            $pager->getNbPages(),
            $this->getPageParameterName(),
            $this->getLimitParameterName(),
            $route->isAbsolute(),
            $pager->getNbResults()
        );
    }

    public function getPageParameterName(): ?string
    {
        return $this->pageParameterName;
    }

    public function getLimitParameterName(): ?string
    {
        return $this->limitParameterName;
    }
}
