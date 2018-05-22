<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk;

class OrderDomainTest extends TestCase
{
    public function testNewOrderOperation()
    {
        $link     = $this->createMock(Sdk\Hal\HalLink::class);
        $instance = new Sdk\Api\Order\OrderDomain($link);

        $this->assertInstanceOf(Sdk\Order\OrderOperation::class, $instance->newOperations());
    }
}
