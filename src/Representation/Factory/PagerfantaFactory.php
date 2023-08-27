<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Representation\Factory;

use Zuruuh\Hateoas\Configuration\Route;
use Zuruuh\Hateoas\Representation\CollectionRepresentation;
use Zuruuh\Hateoas\Representation\PaginatedRepresentation;
use Pagerfanta\Pagerfanta;

class PagerfantaFactory
{
    public function __construct(private readonly ?string $pageParameterName = null, private readonly ?string $limitParameterName = null)
    {
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
