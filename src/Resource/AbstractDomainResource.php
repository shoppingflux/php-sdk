<?php
namespace ShoppingFeed\Sdk\Resource;

use Jsor\HalClient\HalLink;

abstract class AbstractDomainResource
{
    const PER_PAGE = 100;

    /**
     * @var HalLink
     */
    protected $link;

    /**
     * @var string
     */
    protected $resourceClass = '';

    /**
     * @param HalLink $link
     */
    public function __construct(HalLink $link)
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
     * @param     $fromPage
     *
     * @param int $perPage
     *
     * @return PaginatedResourceCollection
     */
    public function getAll($fromPage = 1, $perPage = self::PER_PAGE)
    {
        $resource = $this->createPaginator($fromPage, $perPage);
        while ($resource) {
            foreach ($resource as $item) {
                yield $item;
            }
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
