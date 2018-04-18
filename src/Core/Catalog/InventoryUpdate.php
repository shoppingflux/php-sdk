<?php
namespace ShoppingFeed\Sdk\Core\Catalog;

use Jsor\HalClient\HalLink;
use ShoppingFeed\Sdk\Core\Operation\AbstractBulkOperation;

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
     * @return \ArrayObject|mixed
     */
    public function execute(HalLink $link)
    {
        return $this->chunk(
            function (array $chunk, InventoryCollection $collection) use ($link) {
                $response = $link->put([], $this->createHttpBody($chunk));
                $collection->merge(new InventoryCollection($this->getRelated($response)));
            },
            new InventoryCollection()
        );
    }
}
