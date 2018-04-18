<?php
namespace ShoppingFeed\Sdk\Api\Catalog;

use ShoppingFeed\Sdk\Core\Catalog;
use ShoppingFeed\Sdk\Core\Resource\AbstractDomainResource;

/**
 * @method Catalog\InventoryResource[] getIterator()
 * @method Catalog\InventoryResource[] getAll($page, $limit)
 */
class InventoryDomain extends AbstractDomainResource
{
    /**
     * @var string
     */
    protected $resourceClass = Catalog\InventoryResource::class;

    /**
     * @param string $reference the resource reference
     *
     * @return null|Catalog\InventoryResource
     */
    public function getByReference($reference)
    {
        $resource = $this->link->get([], ['query' => ['reference' => $reference]]);
        if ($resource->getProperty('count') > 0) {
            return new Catalog\InventoryResource(
                $resource->getFirstResource('inventory'),
                false
            );
        }

        return null;
    }

    /**
     * @param Catalog\InventoryUpdate $operation
     *
     * @return \ArrayObject|mixed
     */
    public function execute(Catalog\InventoryUpdate $operation)
    {
        return $operation->execute($this->link);
    }
}
