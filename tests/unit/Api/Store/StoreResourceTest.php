<?php
namespace ShoppingFeed\Sdk\Test\Api\Store;

use ShoppingFeed\Sdk;

class StoreResourceTest extends Sdk\Test\Api\AbstractResourceTest
{
    public function setUp()
    {
        $this->props = [
            'id'      => 10,
            'name'    => 'abc123',
            'country' => 'FR',
            'status'  => 'active',
        ];
    }

    public function testPropertiesGetters()
    {
        $this->initHalResourceProperties();

        $instance = new Sdk\Api\Store\StoreResource($this->halResource);

        $this->assertEquals($this->props['id'], $instance->getId());
        $this->assertEquals($this->props['name'], $instance->getName());
        $this->assertEquals($this->props['country'], $instance->getCountryCode());
        $this->assertTrue($instance->isActive());
    }

    public function testGetInventoryApi()
    {
        /** @var Sdk\Hal\HalResource|\PHPUnit_Framework_MockObject_MockObject $halResource */
        $halResource = $this->createMock(Sdk\Hal\HalResource::class);
        $halResource
            ->expects($this->once())
            ->method('getLink')
            ->with('inventory')
            ->willReturn(
                $this->createMock(Sdk\Hal\HalLink::class)
            );

        $instance = new Sdk\Api\Store\StoreResource($halResource);

        $this->assertInstanceOf(Sdk\Api\Catalog\InventoryDomain::class, $instance->getInventoryApi());
    }

    public function testGetOrderApi()
    {
        /** @var Sdk\Hal\HalResource|\PHPUnit_Framework_MockObject_MockObject $halResource */
        $halResource = $this->createMock(Sdk\Hal\HalResource::class);
        $halResource
            ->expects($this->once())
            ->method('getLink')
            ->with('order')
            ->willReturn(
                $this->createMock(Sdk\Hal\HalLink::class)
            );

        $instance = new Sdk\Api\Store\StoreResource($halResource);

        $this->assertInstanceOf(Sdk\Api\Order\OrderDomain::class, $instance->getOrderApi());
    }
}
