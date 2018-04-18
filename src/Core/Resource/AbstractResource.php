<?php
namespace ShoppingFeed\Sdk\Core\Resource;

use Jsor\HalClient\HalLink;
use Jsor\HalClient\HalResource;

abstract class AbstractResource implements \JsonSerializable
{
    /**
     * @var HalResource
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $isPartial;

    /**
     * @param HalResource $resource
     * @param bool        $isPartial
     */
    public function __construct(HalResource $resource, $isPartial = true)
    {
        $this->resource  = $resource;
        $this->isPartial = $isPartial;
    }

    /**
     * Refresh the resource state from server data, then return it as new object
     *
     * @return static
     */
    public function refresh()
    {
        $instance = clone $this;
        $instance->initialize(true);

        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->resource->getProperties();
    }

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    protected function getProperty($property)
    {
        return $this->resource->getProperty($property);
    }

    /**
     * @param string $property
     * @param        $value
     *
     * @return bool
     */
    protected function propertyMatch($property, $value)
    {
        return $this->getProperty($property) === $value;
    }

    /**
     * @param bool $force
     *
     * @return $this
     */
    protected function initialize($force = false)
    {
        if (true === $force || true === $this->isPartial) {
            $this->resource  = $this->resource->get();
            $this->isPartial = false;
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return HalLink|null
     */
    protected function getLink($name)
    {
        return $this->resource->getFirstLink($name);
    }
}
