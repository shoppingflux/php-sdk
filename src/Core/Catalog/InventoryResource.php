<?php
namespace ShoppingFeed\Sdk\Core\Catalog;

use ShoppingFeed\Sdk\Core\Resource\AbstractResource;

class InventoryResource extends AbstractResource
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->getProperty('id');
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->getProperty('reference');
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->getProperty('quantity');
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt()
    {
        return date_create_immutable($this->getProperty('updatedAt'));
    }
}
