<?php
namespace ShoppingFeed\Sdk\Api\Store;

use ShoppingFeed\Sdk\Api\Catalog\InventoryDomain;
use ShoppingFeed\Sdk\Resource\AbstractResource;

class StoreResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->getProperty('name');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getProperty('id');
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getProperty('status') === 'active';
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getProperty('country');
    }

    /**
     * @return InventoryDomain
     */
    public function getInventory()
    {
        return new InventoryDomain(
            $this->resource->getLink('inventory')
        );
    }
}
