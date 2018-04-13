<?php
namespace ShoppingFeed\Sdk\Resource;

use Jsor\HalClient\HalLink;
use ShoppingFeed\Paginator\PaginatedIterator;
use ShoppingFeed\Paginator\Paginator;

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
     * @return \Traversable
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
     * @return PaginatedIterator
     */
    public function getAll($fromPage = 1, $perPage = self::PER_PAGE)
    {
        return new PaginatedIterator($this->createPaginator($fromPage, $perPage));
    }

    /**
     * @param int $page
     * @param int $perPage
     *
     * @return Paginator
     */
    private function createPaginator($page = 1, $perPage = self::PER_PAGE)
    {
        $paginator = new Paginator(new ResourcePaginatorAdapter(
            $this->link,
            $this->resourceClass
        ));

        $paginator->setCurrentPage($page);
        $paginator->setItemsPerPage($perPage);

        return $paginator;
    }
}
