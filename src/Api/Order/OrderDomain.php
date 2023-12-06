<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Hal\HalResource;
use ShoppingFeed\Sdk\Resource\AbstractDomainResource;

/**
 * @method OrderResource[] getIterator()
 * @method OrderResource[] getAll($criteria = [])
 * @method OrderResource[] getPage(array $criteria = [])
 * @method OrderResource[] getPages(array $criteria = [])
 * @method OrderResource getOne($identity)
 */
class OrderDomain extends AbstractDomainResource
{
    /**
     * @var string
     */
    protected $resourceClass = OrderResource::class;

    /** @return ShipmentResource[] */
    public function getShipmentsByOrder(int $orderId): array
    {
        $link  = $this->link->withAddedHref($orderId . '/shipment');

        /** @var HalResource|null $response */
        $response  = $link->get();
        $shipments = [];

        foreach ($response ? $response->getAllResources() : [] as $resources) {
            foreach ($resources as $resource) {
                $shipments[] = new ShipmentResource($resource);
            }
        }

        return $shipments;
    }

    /**
     * @param OrderOperation $operation
     *
     * @return OrderOperationResult
     */
    public function execute(OrderOperation $operation): OrderOperationResult
    {
        return $operation->execute($this->link);
    }

}
