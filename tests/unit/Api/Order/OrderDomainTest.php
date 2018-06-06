<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\OrderDomain;
use ShoppingFeed\Sdk\Api\Order\OrderOperation;
use ShoppingFeed\Sdk\Api\Order\OrderTicketCollection;
use ShoppingFeed\Sdk\Hal\HalLink;

class OrderDomainTest extends TestCase
{
    public function testExecute()
    {
        $link       = $this->createMock(HalLink::class);
        $operations = $this->createMock(OrderOperation::class);
        $operations
            ->expects($this->once())
            ->method('execute')
            ->with($link)
            ->willReturn($this->createMock(OrderTicketCollection::class));

        $instance = new OrderDomain($link);
        $instance->execute($operations);
    }
}
