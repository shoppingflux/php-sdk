<?php
namespace ShoppingFeed\Sdk\Resource;

use ShoppingFeed\Sdk\Hal;

abstract class AbstractDomainResource
{
    const PER_PAGE = 200;

    /**
     * @var Hal\HalLink
     */
    protected $link;

    /**
     * @var string
     */
    protected $resourceClass = '';

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @param Hal\HalLink $link
     */
    public function __construct(Hal\HalLink $link)
    {
        $this->link = $link;
    }

    /**
     * @param int $page
     * @param int $perPage
     *
     * @return PaginatedResourceCollection
     */
    public function getPage($page = 1, $perPage = self::PER_PAGE)
    {
        return $this->createPaginator($page, $perPage);
    }

    /**
     * @param int $fromPage
     * @param int $perPage
     *
     * @return AbstractResource[]|\Traversable
     */
    public function getAll($fromPage = 1, $perPage = self::PER_PAGE)
    {
        foreach ($this->getPages($fromPage, $perPage) as $collection) {
            foreach ($collection as $item) {
                yield $item;
            }
        }
    }

    /**
     * @param array $filters
     */
    public function addListFilters($filters)
    {
        $this->filters = array_merge($this->filters, $filters);
    }

    public function resetListFilters()
    {
        $this->filters = [];
    }

    /**
     * @param int $fromPage
     * @param int $perPage
     *
     * @return PaginatedResourceCollection[]|\Traversable
     */
    public function getPages($fromPage = 1, $perPage = self::PER_PAGE)
    {
        $resource = $this->createPaginator($fromPage, $perPage);
        while ($resource) {
            yield $resource;
            $resource = $resource->next();
        }
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return PaginatedResourceCollection
     */
    private function createPaginator($page = 1, $limit = self::PER_PAGE)
    {
        $this->link->setFilters($this->filters);
        $resource = $this->link->get([], [
            'query' => array_map('intval', compact('page', 'limit'))
        ]);

        if (! $resource) {
            return null;
        }

        return new PaginatedResourceCollection(
            $resource,
            $this->resourceClass
        );
    }
}
