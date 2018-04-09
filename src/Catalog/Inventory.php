<?php
namespace ShoppingFeed\Sdk\Catalog;

class Inventory implements \JsonSerializable
{
    private $reference;

    private $quantity;

    /**
     * @param $reference
     * @param $quantity
     */
    public function __construct(string $reference, int $quantity)
    {
        $this->reference = trim($reference);
        $this->quantity  = max(0, $quantity);
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'reference' => $this->reference,
            'quantity'  => $this->quantity
        ];
    }
}
