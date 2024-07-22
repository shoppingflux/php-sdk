<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Order\Shipment\ShipmentDomain;
use ShoppingFeed\Sdk\Api\Order\Shipment\ShipmentResource;
use ShoppingFeed\Sdk\Operation\OperationInterface;
use ShoppingFeed\Sdk\Resource;

/**
 * @method OrderResource[] getIterator()
 * @method OrderResource[] getAll($criteria = [])
 * @method OrderResource[] getPage(array $criteria = [])
 * @method OrderResource[] getPages(array $criteria = [])
 * @method OrderResource getOne($identity)
 */
class OrderDomain extends Resource\AbstractDomainResource
{
    /**
     * @var string
     */
    protected $resourceClass = OrderResource::class;

    /** @return Resource\PaginatedResourceIterator<ShipmentResource> */
    public function getShipmentsByOrder(int $orderId)
    {
        return (new ShipmentDomain($this->link->withAddedHref($orderId . '/shipment')))->getAll();
    }

    public function execute(OperationInterface $operation): OrderOperationResult
    {
        return $operation->execute($this->link);
    }
}
