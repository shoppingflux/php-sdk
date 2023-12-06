<?php

namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\OrderDomain;
use ShoppingFeed\Sdk\Api\Order\OrderOperation;
use ShoppingFeed\Sdk\Api\Order\OrderOperationResult;
use ShoppingFeed\Sdk\Api\Order\ShipmentResource;
use ShoppingFeed\Sdk\Hal\HalLink;
use ShoppingFeed\Sdk\Hal\HalResource;

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
            ->willReturn($this->createMock(OrderOperationResult::class));

        $instance = new OrderDomain($link);
        $instance->execute($operations);
    }

    public function testShipmentGetters(): void
    {
        $orderId = 1234;

        $link = $this->createMock(HalLink::class);
        $link
            ->expects($this->once())
            ->method('withAddedHref')
            ->with($orderId . '/shipment')
            ->willReturn($link);

        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn($response = $this->createMock(HalResource::class));

        $resource1 = $this->createMock(HalResource::class);
        $resource2 = $this->createMock(HalResource::class);

        $response
            ->expects($this->once())
            ->method('getAllResources')
            ->willReturn([[$resource1, $resource2]]);

        $expected = [new ShipmentResource($resource1), new ShipmentResource($resource2)];
        $instance = new OrderDomain($link);

        $this->assertEquals($expected, $instance->getShipmentsByOrder($orderId));
    }

    public function testShipmentGettersEmpty(): void
    {
        $orderId = 1234;

        $link = $this->createMock(HalLink::class);
        $link
            ->expects($this->once())
            ->method('withAddedHref')
            ->with($orderId . '/shipment')
            ->willReturn($link);

        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $instance = new OrderDomain($link);

        $this->assertEquals([], $instance->getShipmentsByOrder($orderId));
    }
}
