<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Resource\AbstractResource;

class OrderResource extends AbstractResource
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
        $dateValue = $this->getProperty('acknowledgedAt');
        return date_create_immutable(is_null($dateValue) ? 'now' : $dateValue);
    }

    /**
     * @return null|\DateTimeImmutable
     */
    public function getUpdateddAt()
    {
        $dateValue = $this->getProperty('updatedAt');
        return date_create_immutable(is_null($dateValue) ? 'now' : $dateValue);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return date_create_immutable($this->getProperty('createdAt'));
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
}
