<?php
namespace ShoppingFeed\Sdk\Catalog;

use ShoppingFeed\Sdk\Resource\AbstractDomainResource;

class InventoryDomain extends AbstractDomainResource
{
    /**
     * @var string
     */
    protected $resourceClass = InventoryResource::class;

    /**
     * @param string $reference the resource reference
     *
     * @return null|InventoryResource
     */
    public function getByReference($reference)
    {
        $resource = $this->link->get([], ['query' => ['reference' => $reference]]);
        if ($resource->getProperty('count') > 0) {
            return new InventoryResource(
                $resource->getFirstResource('inventory'),
                false
            );
        }

        return null;
    }

    /**
     * @param InventoryUpdate $operation
     *
     * @return \ArrayObject|mixed
     */
    public function execute(InventoryUpdate $operation)
    {
        return $operation->execute($this->link);
    }
}
