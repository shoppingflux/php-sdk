<?php
namespace ShoppingFeed\Sdk\Api\Catalog;

use Jsor\HalClient\HalLink;
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
     * @return string
     */
    public function getRelatedResource()
    {
        return 'inventory';
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
     * @param HalLink $link
     *
     * @return InventoryCollection
     */
    public function execute(HalLink $link)
    {
        $resources = [];
        $this->eachBatch(
            function (array $chunk) use ($link, &$resources) {
                $response   = $link->put([], $this->createHttpBody($chunk));
                $collection = new InventoryCollection($this->getRelated($response));
                array_push($resources, ...iterator_to_array($collection));
            }
        );

        return new InventoryCollection($resources);
    }
}
