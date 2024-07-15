<?php

namespace ShoppingFeed\Sdk\Api\Order\Identifier;

class Id implements OrderIdentifier
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function toArray(): array
    {
        return ['id' => $this->id];
    }
}
