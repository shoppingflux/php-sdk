<?php
namespace ShoppingFeed\Sdk\Test\Resource;

use ShoppingFeed\Sdk\Resource\AbstractResource;

class ResourceMock extends AbstractResource
{
    public function getProperty($property)
    {
        return parent::getProperty($property);
    }

    public function propertyMatch($property, $value)
    {
        return parent::propertyMatch($property, $value);
    }

    public function getPropertyDatetime($property)
    {
        return parent::getPropertyDatetime($property);
    }

    public function initialize($force = false)
    {
        return parent::initialize($force);
    }

    public function isPartial()
    {
        return parent::isPartial();
    }

    public function getLink($name)
    {
        return parent::getLink($name);
    }
}
