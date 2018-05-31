<?php
namespace ShoppingFeed\Sdk\Api\Catalog;

use ShoppingFeed\Sdk\Api\Catalog as ApiCatalog;
use ShoppingFeed\Sdk\Catalog;
use ShoppingFeed\Sdk\Resource\AbstractDomainResource;

/**
 * @method ApiCatalog\InventoryResource[] getIterator()
 * @method ApiCatalog\InventoryResource[] getAll($page = 1, $limit = 100)
 */
class InventoryDomain extends AbstractDomainResource
{
    /**
     * @var string
     */
    protected $resourceClass = ApiCatalog\InventoryResource::class;

    /**
     * @param string $reference the resource reference
     *
     * @return null|InventoryResource
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getByReference($reference)
    {
        $resource = $this->link->get([], ['query' => ['reference' => $reference]]);
        if ($resource->getProperty('count') > 0) {
            return new ApiCatalog\InventoryResource(
                $resource->getFirstResource('inventory'),
                false
            );
        }

        return null;
    }

    /**
     * Execute requested update
     *
     * @param InventoryUpdate $operation
     *
     * @return InventoryCollection
     */
    public function execute(InventoryUpdate $operation)
    {
        return $operation->execute($this->link);
    }
}
