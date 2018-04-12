<?php
namespace ShoppingFeed\Sdk\Resource;

use Jsor\HalClient\HalResource;

abstract class AbstractPaginatedCollection extends AbstractResource implements \Countable
{
    protected $resourceClass = '';

    public function __construct(HalResource $resource)
    {
        parent::__construct($resource, false);
    }

    public function count()
    {
        return $this->getProperty('total');
    }

    /**
     * @return null|AbstractResource
     */
    public function next()
    {
        return $this->getPaginationLink('next');
    }

    /**
     * @return null|AbstractResource
     */
    public function prev()
    {
        return $this->getPaginationLink('prev');
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    private function getPaginationLink($name)
    {
        $resource = $this->resource->getLink($name)[0]->get();
        if ($resource) {
            return new $this->resourceClass($resource, false);
        }

        return null;
    }
}
