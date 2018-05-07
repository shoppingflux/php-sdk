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
     * @var Catalog\InventoryUpdate
     */
    private $inventoryUpdate;

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
     * @return InventoryDomain
     */
    public function newInventoryUpdate()
    {
        $this->inventoryUpdate = new Catalog\InventoryUpdate();

        return $this;
    }

    /**
     * @param string $reference
     * @param int    $quantity
     *
     * @return InventoryDomain
     */
    public function add($reference, $quantity)
    {
        if (! isset($this->inventoryUpdate)) {
            $this->newInventoryUpdate();
        }

        $this->inventoryUpdate->add($reference, $quantity);

        return $this;
    }

    /**
     * @return InventoryCollection
     */
    public function execute()
    {
        return $this->inventoryUpdate->execute($this->link);
    }
}
