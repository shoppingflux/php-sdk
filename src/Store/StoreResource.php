<?php
namespace ShoppingFeed\Sdk\Store;

use ShoppingFeed\Paginator;
use ShoppingFeed\Sdk\Catalog\InventoryDomain;
use ShoppingFeed\Sdk\Catalog\InventoryResource;
use ShoppingFeed\Sdk\Operation\AbstractOperation;
use ShoppingFeed\Sdk\Resource\AbstractResource;
use ShoppingFeed\Sdk\Resource\ResourcePaginatorAdapter;

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
            $this->resource->getFirstLink('inventory')
        );
    }
}
