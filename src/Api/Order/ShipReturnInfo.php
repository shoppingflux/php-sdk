<?php

namespace ShoppingFeed\Sdk\Api\Order;

class ShipReturnInfo
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
     * @return array{carrier: string|null,trackingNumber: string|null}
     */
    public function toArray(): array
    {
        return [
            'carrier'        => $this->carrier,
            'trackingNumber' => $this->trackingNumber,
        ];
    }
}
