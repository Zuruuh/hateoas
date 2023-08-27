<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Representation;

use JMS\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 * @Serializer\AccessorOrder("custom", custom = {"page", "limit", "pages", "total"})
 *
 * @Hateoas\Relation(
 *      "first",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(1))",
 *          absolute = "expr(object.isAbsolute())"
 *      )
 * )
 * @Hateoas\Relation(
 *      "last",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getPages()))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getPages() === null)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "next",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getPage() + 1))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getPages() !== null && (object.getPage() + 1) > object.getPages())"
 *      )
 * )
 * @Hateoas\Relation(
 *      "previous",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getPage() - 1))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr((object.getPage() - 1) < 1)"
 *      )
 * )
 */
class PaginatedRepresentation extends AbstractSegmentedRepresentation
{
    private readonly string $pageParameterName;

    /**
     * @param mixed $inline
     */
    public function __construct(
        $inline,
        string $route,
        array $parameters,
        /**
         * @Serializer\Expose
         * @Serializer\Type("integer")
         * @Serializer\XmlAttribute
         */
        private readonly ?int $page,
        ?int $limit,
        /**
         * @Serializer\Expose
         * @Serializer\Type("integer")
         * @Serializer\XmlAttribute
         */
        private readonly ?int $pages,
        ?string $pageParameterName = null,
        ?string $limitParameterName = null,
        bool $absolute = false,
        ?int $total = null
    ) {
        parent::__construct($inline, $route, $parameters, $limit, $total, $limitParameterName, $absolute);
        $this->pageParameterName  = $pageParameterName ?: 'page';
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param  null  $page
     * @param  null  $limit
     *
     * @return array
     */
    public function getParameters($page = null, ?int $limit = null): array
    {
        $parameters = parent::getParameters($limit);

        unset($parameters[$this->pageParameterName]);
        $parameters[$this->pageParameterName] = $page ?? $this->getPage();

        $this->moveParameterToEnd($parameters, $this->getLimitParameterName());

        return $parameters;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getPageParameterName(): string
    {
        return $this->pageParameterName;
    }
}
