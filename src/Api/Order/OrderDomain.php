<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Order\Shipment\ShipmentDomain;
use ShoppingFeed\Sdk\Api\Order\Shipment\ShipmentResource;
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
        return (new ShipmentDomain($this->link->withAddedHref($orderId . '/shipment')))->getAll();
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
