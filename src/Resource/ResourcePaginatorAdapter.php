<?php
namespace ShoppingFeed\Sdk\Resource;

use Jsor\HalClient\HalLink;
use ShoppingFeed\Paginator\Adapter\AbstractPaginatorAdapter;

class ResourcePaginatorAdapter extends AbstractPaginatorAdapter
{
    /**
     * @var HalLink
     */
    private $link;

    /**
     * @var string
     */
    private $resourceClass;

    /**
     * @param HalLink $collection
     * @param         $resourceClass
     */
    public function __construct(HalLink $collection, $resourceClass)
    {
        $this->link          = $collection;
        $this->resourceClass = (string) $resourceClass;
    }

    public function getIterator()
    {
        $resource = $this->link->get([], [
            'query' => [
                'limit' => $this->getLimit(),
                'page'  => $this->getCurrentPage()
            ]
        ]);

        $collection = current($resource->getResources());
        foreach ($collection as $item) {
            yield new $this->resourceClass($item);
        }
    }

    public function count()
    {
        $resource = $this->link->get([], [
            'query' => [
                'limit' => 0,
                'page'  => 1
            ]
        ]);

        return (int) $resource->getProperty('total');
    }
}
