<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Channel\ChannelResource;
use ShoppingFeed\Sdk\Api\Order\Shipment\ShipmentDomain;
use ShoppingFeed\Sdk\Api\Order\Shipment\ShipmentResource;
use ShoppingFeed\Sdk\Resource;

class OrderResource extends Resource\AbstractResource
{
    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->getProperty('id');
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return (string) $this->getProperty('reference');
    }

    /**
     * @return null|string
     */
    public function getStoreReference()
    {
        return $this->getProperty('storeReference');
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return (string) $this->getProperty('status');
    }

    /**
     * @return null|\DateTimeImmutable
     */
    public function getAcknowledgedAt()
    {
        return $this->getPropertyDatetime('acknowledgedAt');
    }

    /**
     * @return null|\DateTimeImmutable
     */
    public function getUpdatedAt()
    {
        return $this->getPropertyDatetime('updatedAt');
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->getPropertyDatetime('createdAt');
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLatestShipDate()
    {
        return $this->getPropertyDatetime('latestShipDate');
    }

    /**
     * @return array
     */
    public function getShippingAddress()
    {
        return $this->getProperty('shippingAddress');
    }

    /**
     * @return array
     */
    public function getBillingAddress()
    {
        return $this->getProperty('billingAddress');
    }

    /**
     * @return array
     */
    public function getPaymentInformation()
    {
        return $this->getProperty('payment');
    }

    /**
     * @return array
     */
    public function getShipment()
    {
        return $this->getProperty('shipment');
    }

    /**
     * Fetch order items details
     * The resource has to be loaded to access to items collection
     *
     * @return OrderItemCollection|OrderItem[]
     */
    public function getItems()
    {
        return OrderItemCollection::fromProperties(
            $this->getProperty('items', true) ?: []
        );
    }

    /**
     * @return array
     */
    public function getItemsReferencesAliases()
    {
        return $this->getProperty('itemsReferencesAliases');
    }

    /**
     * @return ChannelResource A partial representation of the channel resource
     */
    public function getChannel()
    {
        return new ChannelResource(
            $this->resource->getFirstResource('channel')
        );
    }

    /**
     * @return Resource\PaginatedResourceIterator<ShipmentResource>
     */
    public function getShipments()
    {
        $link = $this->resource->getLink('self');

        return (new ShipmentDomain($link->withAddedHref('/shipment')))->getAll();
    }
}
