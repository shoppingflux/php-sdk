<?php

namespace ShoppingFeed\Sdk\Api\Order;

use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    /**
     * @var OrderItem
     */
    private $instance;

    public function setUp(): void
    {
        $this->instance = new OrderItem(
            123,
            'a',
            '',
            2,
            9.99,
            null,
            7.99,
            0,
            'A00001',
            ['taxRate' => 20],
            'ASPIRO 2000',
            'http://url.com/image.png'
        );
    }

    public function testGetters()
    {
        $this->assertSame(123, $this->instance->getId());
        $this->assertSame('a', $this->instance->getReference());
        $this->assertSame('', $this->instance->getStatus());
        $this->assertSame(2, $this->instance->getQuantity());
        $this->assertSame(9.99, $this->instance->getUnitPrice());
        $this->assertNull($this->instance->getCommission());
        $this->assertSame(7.99, $this->instance->getTaxAmount());
        $this->assertSame(0, $this->instance->getEcotaxAmount());
        $this->assertSame('A00001', $this->instance->getChannelReference());
        $this->assertSame(['taxRate' => 20], $this->instance->getAdditionalFields());
        $this->assertSame('ASPIRO 2000', $this->instance->getName());
        $this->assertSame('http://url.com/image.png', $this->instance->getImage());

        $this->assertSame(19.98, $this->instance->getTotalPrice());
    }
}

