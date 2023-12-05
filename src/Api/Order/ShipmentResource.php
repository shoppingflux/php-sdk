<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Channel\ChannelResource;
use ShoppingFeed\Sdk\Resource\AbstractResource;

class ShipmentResource extends AbstractResource
{

    /**
     * @return string
     */
    public function getCarrier()
    {
        return (string) $this->getProperty('carrier');
    }

    /**
     * @return string
     */
    public function getTrackingNumber()
    {
        return (string) $this->getProperty('trackingNumber');
    }

    /**
     * @return string
     */
    public function getTrackingUrl()
    {
        return (string) $this->getProperty('trackingUrl');
    }

    /**
     * @return array
     */
    public function getReturnInfo()
    {
        return (array) $this->getProperty('returnInfo');
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return (string) $this->getProperty('createdAt');
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return (array) $this->getProperty('items');
    }
}

