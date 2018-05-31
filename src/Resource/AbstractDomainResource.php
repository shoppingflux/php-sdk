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
     * @param array $criterias Pagination criterias
     *
     * @return PaginatedResourceCollection[]|\Traversable
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPages(array $criterias)
    {
        $criterias = new PaginationCriterias($criterias);
        $resource  = $this->createPaginator($criterias);
        while ($resource) {
            yield $resource;
            $resource = $resource->next();
        }
    }

    /**
     * @param PaginationCriterias $criterias
     *
     * @return null|PaginatedResourceCollection
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function createPaginator(PaginationCriterias $criterias)
    {
        $resource = $this->link->get([],['query' => $criterias->toArray()]);

        if (! $resource) {
            return null;
        }

        return new PaginatedResourceCollection(
            $resource,
            $this->resourceClass
        );
    }
}
