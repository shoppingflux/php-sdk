<?php
namespace ShoppingFeed\Sdk\Resource;

use Jsor\HalClient\HalResource;

class PaginatedResourceCollection extends AbstractResource implements \IteratorAggregate
{
    /**
     * @var string
     */
    private $resourceClass;

    /**
     * @param HalResource $resource
     * @param string      $resourceClass
     */
    public function __construct(HalResource $resource, $resourceClass)
    {
        $this->resourceClass = (string) $resourceClass;

        parent::__construct($resource, false);
    }

    /**
     * @return null|PaginatedResourceCollection
     */
    public function next()
    {
        if (! $this->resource->hasLink('next')) {
            return null;
        }

        $link = $this->resource->getFirstLink('next');
        if (! $resource = $link->get()) {
            return null;
        }

        return new static($resource, $this->resourceClass);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $data = current($this->resource->getResources()) ?: [];
        foreach ($data as $item) {
            yield new $this->resourceClass($item);
        }
    }
}
