<?php
namespace ShoppingFeed\Sdk\Api\Order;

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

    /**
     * @var string
     */
    protected $shipmentResourceClass = ShipmentResource::class;


    public function getShipmentsByOrder($identity)
    {
        $link  = $this->link->withAddedHref($identity . '/shipment');
        $class = $this->shipmentResourceClass;

        $resource = $link->get();
        $shipments = [];

        /** @var HalResource[] $resources */
        foreach ($resource->getAllResources() as $resources) {
            foreach ($resources as $resource) {
                $shipments[] = new $class($resource);
            }
        }

        return $shipments;
    }

    /**
     * @param OrderOperation $operation
     *
     * @return OrderOperationResult
     */
    public function execute(OrderOperation $operation)
    {
        return $operation->execute($this->link);
    }

}
