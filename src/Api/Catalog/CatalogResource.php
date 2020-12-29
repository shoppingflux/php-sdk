<?php
namespace ShoppingFeed\Sdk\Api\Catalog;

use ShoppingFeed\Sdk\Resource\AbstractResource;

class CatalogResource extends AbstractResource
{
    public function getOperationLink()
    {
        return $this->resource->getLink('operation');
    }

    public function getId()
    {
        return $this->getProperty('id');
    }
}
