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
     * @param array $criteria
     *
     * @return null|PaginatedResourceCollection
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPage(array $criteria = [])
    {
        $criteria = new PaginationCriteria($criteria);
        return $this->createPaginator($criteria);
    }

    /**
     * @param array $criteria
     *
     * @return AbstractResource[]|\Traversable
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAll(array $criteria = [])
    {
        foreach ($this->getPages($criteria) as $collection) {
            foreach ($collection as $item) {
                yield $item;
            }
        }
    }

    /**
     * @param array $criteria Pagination criteria
     *
     * @return PaginatedResourceCollection[]|\Traversable
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function createPaginator(PaginationCriteria $criteria)
    {
        $resource = $this->link->get([], ['query' => $criteria->toArray()]);

        if (! $resource) {
            return null;
        }

        return new PaginatedResourceCollection(
            $resource,
            $this->resourceClass
        );
    }
}
