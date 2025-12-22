<?php

namespace ShoppingFeed\Sdk\Resource;

use ShoppingFeed\Sdk\Hal;

abstract class AbstractDomainResource
{
    public const PER_PAGE = 200;

    /**
     * Paginated collection class to use
     *
     * @var string
     */
    protected $paginatorClass = PaginatedResourceCollection::class;

    /**
     * Paginated iterator class to use
     *
     * @var string
     */
    protected $iteratorClass = PaginatedResourceIterator::class;

    /** @var Hal\HalLink */
    protected $link;

    /** @var string */
    protected $resourceClass = '';

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
     *
     * @return PaginatedResourceCollection
     */
    public function getPage(array $criteria = [])
    {
        return $this->createPaginator(
            new PaginationCriteria($criteria),
        );
    }

    /**
     *
     * @return AbstractResource[]|\Traversable
     */
    public function getAll(array $filters = [])
    {
        $criteria = new PaginationCriteria(isset($filters['filters']) ? $filters : ['filters' => $filters]);

        return $this->createIterator($criteria);
    }

    /**
     * @param array $criteria Pagination criteria
     *
     * @return PaginatedResourceCollection[]|\Traversable
     */
    public function getPages(array $criteria = [])
    {
        $resource = $this->createPaginator(new PaginationCriteria($criteria));

        while ($resource) {
            yield $resource;

            $resource = $resource->next();
        }
    }

    /**
     *
     * @return null|PaginatedResourceCollection
     */
    protected function createPaginator(PaginationCriteria $criteria)
    {
        $resource = $this->link->get([], ['query' => $criteria->getQueryParams()]);

        if (! $resource) {
            return null;
        }

        return new $this->paginatorClass(
            $resource,
            $this->resourceClass,
        );
    }

    /**
     *
     * @return null|PaginatedResourceIterator
     */
    protected function createIterator(PaginationCriteria $criteria)
    {
        $paginator = $this->createPaginator($criteria);

        if ($paginator) {
            return new $this->iteratorClass($paginator);
        }

        return null;
    }
}
