<?php

namespace ShoppingFeed\Sdk\Api\Order\Shipment;

class ShipmentItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(int $id, int $quantity)
    {
        $this->id       = $id;
        $this->quantity = $quantity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}