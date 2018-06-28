<?php
namespace ShoppingFeed\Sdk\Api\Order;

/**
 * Class that represents the cart items of the order.
 */
class OrderItem
{
    private $reference;
    private $quantity;
    private $unitPrice;

    /**
     * @param string $reference The product's reference
     * @param int    $quantity  Number product's occurrence in cart
     * @param float  $price     The price for a single unit of the reference (not price * quantity)
     */
    public function __construct($reference, $quantity, $price)
    {
        $this->reference = $reference;
        $this->quantity  = $quantity;
        $this->unitPrice = $price;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @return float|int
     */
    public function getTotalPrice()
    {
        return $this->getUnitPrice() * $this->getQuantity();
    }
}
