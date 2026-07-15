<?php

namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\OrderOperation;

class OrderOperationTest extends TestCase
{
    public function testCount(): void
    {
        $operation = new OrderOperation();
        $operation
            ->accept('ORDER123', 'ChannelA')
            ->cancel('ORDER456', 'ChannelB')
            ->ship('ORDER789', 'ChannelC');

        $this->assertSame(3, $operation->count());
    }
}
