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
     * @param Hal\HalLink $link
     */
    public function __construct(Hal\HalLink $link)
    {
        $this->link = $link;
    }

    /**
     * Get the resource by it's identity
     *
     * @param mixed $identity a scalar value that identity the resource
     *
     * @return AbstractResource
     */
    public function getOne($identity)
    {
        $link  = $this->link->withAddedHref($identity);
        $class = $this->resourceClass;

        return new $class($link->get());
    }

    /**
     * @param array $criteria
     *
     * @return null|PaginatedResourceCollection
     */
    public function getPage(array $criteria = [])
    {
        $criteria = new PaginationCriteria($criteria);

        return $this->createPaginator($criteria);
    }

    /**
     * @param array $filters
     *
     * @return AbstractResource[]|\Traversable
     */
    public function getAll(array $filters = [])
    {
        $filters = isset($filters['filters']) ? $filters : ['filters' => $filters];
        foreach ($this->getPages($filters) as $collection) {
            foreach ($collection as $item) {
                yield $item;
            }
        }
    }

    /**
     * @param array $criteria Pagination criteria
     *
     * @return PaginatedResourceCollection[]|\Traversable
     */
    public function getPages(array $criteria = [])
    {
        $criteria = new PaginationCriteria($criteria);
        $resource = $this->createPaginator($criteria);
        while ($resource) {
            yield $resource;
            $resource = $resource->next();
        }
    }

    /**
     * @param PaginationCriteria $criteria
     *
     * @return null|PaginatedResourceCollection
     */
    private function createPaginator(PaginationCriteria $criteria)
    {
        $resource = $this->link->get([], ['query' => $criteria->getQueryParams()]);

        if (! $resource) {
            return null;
        }

        return new PaginatedResourceCollection(
            $resource,
            $this->resourceClass
        );
    }
}
