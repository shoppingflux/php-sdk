<?php

namespace ShoppingFeed\Sdk\Api\Order\Identifier;

final class Id implements OrderIdentifier
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return array{id: int}
     */
    public function toArray(): array
    {
        return ['id' => $this->id];
    }
}
