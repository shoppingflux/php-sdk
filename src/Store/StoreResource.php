<?php
namespace ShoppingFeed\Sdk\Store;

use ShoppingFeed\Sdk\Operation\AbstractOperation;
use ShoppingFeed\Sdk\Resource\AbstractResource;

class StoreResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getProperty('name');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getProperty('id');
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getProperty('status') === 'active';
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->getProperty('country');
    }

    /**
     * @param AbstractOperation $operation
     *
     * @return mixed
     */
    public function execute(AbstractOperation $operation)
    {
        return $operation->execute(
            $this->resource->getFirstLink(
                $operation->getRelatedResource()
            )
        );
    }
}
