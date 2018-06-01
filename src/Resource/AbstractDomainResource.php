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
     * @param array $criterias
     *
     * @return null|PaginatedResourceCollection
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPage(array $criterias = [])
    {
        $criterias = new PaginationCriterias($criterias);
        return $this->createPaginator($criterias);
    }

    /**
     * @param array $criterias
     *
     * @return AbstractResource[]|\Traversable
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAll(array $criterias = [])
    {
        foreach ($this->getPages($criterias) as $collection) {
            foreach ($collection as $item) {
                yield $item;
            }
        }
    }

    /**
     * @param array $criterias Pagination criterias
     *
     * @return PaginatedResourceCollection[]|\Traversable
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPages(array $criterias = [])
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
