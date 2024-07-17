<?php

namespace ShoppingFeed\Sdk\Api\Order\Identifier;

interface OrderIdentifier
{
    /**
     * @return array<string, int|string>
     */
    public function toArray(): array;
}
