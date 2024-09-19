<?php
namespace ShoppingFeed\Sdk\Api\Order;

/**
 * Class that represents the cart items of the order.
 */
class OrderItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var float
     */
    private $unitPrice;

    /**
     * @var null|float
     */
    private $commission;

    /**
     * @var float
     */
    private $taxAmount;

    /**
     * @var float
     */
    private $ecotaxAmount;

    /**
     * @var string
     */
    private $channelReference;

    /**
     * @var null|array
     */
    private $additionalFields;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var null|string
     */
    private $image;

    /**
     * @param int $id The product's id
     * @param string $reference The product's reference
     * @param string $status The product's status
     * @param int $quantity Number product's occurrence in cart
     * @param float $price The price for a single unit of the reference (not price * quantity)
     * @param null|float $commission The commission taken by the channel for this item
     * @param float $taxAmount The tax amount of the item price
     * @param float $ecotaxAmount The ecotax amount of the item price
     * @param string $channelReference The channel reference of the product
     * @param null|array $additionalFields A key / value object containing additional marketplace fields for the item
     * @param null|string $name The product's name
     * @param null|string $image The main image of the product
     */
    public function __construct(
        $id,
        $reference,
        $status,
        $quantity,
        $price,
        $commission,
        $taxAmount,
        $ecotaxAmount,
        $channelReference,
        $additionalFields,
        $name,
        $image
    )
    {
        $this->id               = $id;
        $this->reference        = $reference;
        $this->status           = $status;
        $this->quantity         = $quantity;
        $this->unitPrice        = $price;
        $this->commission       = $commission;
        $this->taxAmount        = $taxAmount;
        $this->ecotaxAmount     = $ecotaxAmount;
        $this->channelReference = $channelReference;
        $this->additionalFields = $additionalFields;
        $this->name             = $name;
        $this->image            = $image;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * @return null|float
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @return float
     */
    public function getEcotaxAmount()
    {
        return $this->ecotaxAmount;
    }

    /**
     * @return string
     */
    public function getChannelReference()
    {
        return $this->channelReference;
    }

    /**
     * @return array
     */
    public function getAdditionalFields()
    {
        return $this->additionalFields;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return float|int
     */
    public function getTotalPrice()
    {
        return $this->getUnitPrice() * $this->getQuantity();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'               => $this->getId(),
            'reference'        => $this->getReference(),
            'status'           => $this->getStatus(),
            'quantity'         => $this->getQuantity(),
            'price'            => $this->getUnitPrice(),
            'commission'       => $this->getCommission(),
            'taxAmount'        => $this->getTaxAmount(),
            'ecotaxAmount'     => $this->getEcotaxAmount(),
            'channelReference' => $this->getChannelReference(),
            'additionalFields' => $this->getAdditionalFields(),
            'name'             => $this->getName(),
            'image'            => $this->getImage(),
        ];
    }
}
