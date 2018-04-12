<?php
namespace ShoppingFeed\Sdk\Store;

use ShoppingFeed\Sdk\Operation\AbstractOperation;
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
