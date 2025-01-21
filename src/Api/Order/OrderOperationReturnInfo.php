<?php

namespace ShoppingFeed\Sdk\Api\Order;

class OrderOperationReturnInfo
{
    /**
     * @var ?string
     */
    private $carrier;

    /**
     * @var ?string
     */
    private $trackingNumber;

    public function __construct(?string $carrier, ?string $trackingNumber)
    {
        $this->carrier        = $carrier;
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * @return ?string
     */
    public function getCarrier(): ?string
    {
        return $this->carrier;
    }

    /**
     * @return ?string
     */
    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }
}
