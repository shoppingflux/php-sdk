<?php
namespace ShoppingFeed\Sdk\Catalog;

use ShoppingFeed\Sdk\Resource\AbstractResource;

class InventoryResource extends AbstractResource
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getProperty('id');
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->getProperty('reference');
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->getProperty('quantity');
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return date_create_immutable($this->getProperty('updatedAt'));
    }
}
