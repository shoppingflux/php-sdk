<?php
namespace ShoppingFeed\Sdk\Api\Catalog;

use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Operation\AbstractBulkOperation;

class InventoryUpdate extends AbstractBulkOperation
{
    /**
     * @param array|\Traversable $operations Optional iterable collection composed of:
     *                                       - key (string) : product's reference
     *                                       - value (int)  : product's new quantity
     */
    public function __construct($operations = [])
    {
        foreach ($operations as $reference => $quantity) {
            $this->add($reference, $quantity);
        }
    }

    /**
     * @param string $reference The product's reference
     * @param int    $quantity  The new product's quantity
     *
     * @return $this
     */
    public function add($reference, $quantity)
    {
        $reference = trim($reference);
        $quantity  = max(0, $quantity);

        $this->operations[$reference] = compact('reference', 'quantity');

        return $this;
    }

    /**
     * @param Hal\HalLink $link
     *
     * @return InventoryCollection
     */
    public function execute(Hal\HalLink $link)
    {
        // Create requests per batch
        $requests = [];
        $this->eachBatch(
            function (array $chunk) use ($link, &$requests) {
                $requests[] = $link->createRequest('PUT', [], ['inventory' => $chunk]);
            }
        );

        // Send requests
        $resources = [];
        $link->batchSend(
            $requests,
            function (Hal\HalResource $resource) use (&$resources) {
                array_push($resources, ...$resource->getResources('inventory'));
            },
            null,
            [],
            $this->getPoolSize()
        );

        return new InventoryCollection($resources);
    }
}
