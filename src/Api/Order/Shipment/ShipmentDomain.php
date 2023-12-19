<?php

namespace ShoppingFeed\Sdk\Api\Order\Shipment;

use ShoppingFeed\Sdk\Hal\HalResource;
use ShoppingFeed\Sdk\Resource\AbstractDomainResource;

class ShipmentDomain extends AbstractDomainResource
{
    /** @return ShipmentResource[] */
    public function getAll(array $filters = []): array
    {
        /** @var HalResource|null $response */
        $response  = $this->link->get();
        $shipments = [];

        foreach ($response ? $response->getAllResources() : [] as $resources) {
            foreach ($resources as $resource) {
                $shipments[] = new ShipmentResource($resource);
            }
        }

        return $shipments;
    }
}
