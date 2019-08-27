<?php
namespace ShoppingFeed\Sdk\Api\Task;

use ShoppingFeed\Sdk\Resource\AbstractResource;

class BatchResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getId()
    {
        return $this->getProperty('id');
    }
}
