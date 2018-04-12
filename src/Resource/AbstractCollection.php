<?php
namespace ShoppingFeed\Sdk\Resource;

use Jsor\HalClient\HalResource;
use Traversable;

abstract class AbstractCollection extends AbstractResource implements \Countable, \IteratorAggregate
{
    /**
     * @var string
     */
    protected $resourceClass = '';

    /**
     * @var AbstractResource[]
     */
    protected $resources;

    /**
     * @param AbstractCollection $collection
     *
     * @return static A new instance with merged items
     */
    public function merge(AbstractCollection $collection)
    {
        $this->addResources($collection);

        return $this;
    }

    /**
     * @param array $resources
     */
    public function __construct(array $resources = [])
    {
        $this->resources = [];
        $this->addResources($resources);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->resources);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function(AbstractResource $resource) {
            return $resource->toArray();
        }, $this->resources);
    }

    /**
     * @return Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->resources);
    }

    /**
     * @param iterable $resources
     */
    private function addResources(iterable $resources): void
    {
        $className = $this->resourceClass;
        foreach ($resources as $resource) {
            if ($resource instanceof HalResource) {
                $resource = new $className($resource, true);
            }
            $this->resources[] = $resource;
        }
    }
}
