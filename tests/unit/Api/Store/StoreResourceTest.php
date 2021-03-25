<?php
namespace ShoppingFeed\Sdk\Test\Api\Store;

use ShoppingFeed\Sdk;

class StoreResourceTest extends Sdk\Test\Api\AbstractResourceTest
{
    /**
     * @var string $deletedAt
     */
    private $deletedAt;

    public function setUp()
    {
        $this->deletedAt = '2021-03-05';

        $this->props = [
            'id'        => 10,
            'name'      => 'abc123',
            'country'   => 'FR',
            'status'    => 'active',
            'deletedAt' => $this->deletedAt,
        ];
    }

    public function testPropertiesGetters()
    {
        $this->initHalResourceProperties();

        $instance = new Sdk\Api\Store\StoreResource($this->halResource);

        $this->assertSame($this->props['id'], $instance->getId());
        $this->assertSame($this->props['name'], $instance->getName());
        $this->assertSame($this->props['country'], $instance->getCountryCode());
        $this->assertSame('active', $instance->getStatus());
        $this->assertTrue($instance->isActive());
        $this->assertSame(
            (new \DateTimeImmutable($this->deletedAt))->getTimestamp(),
            $instance->getDeletedAt()->getTimestamp()
        );
    }

    public function testPropertyDeletedAtIsNull()
    {
        $props = [
            'id'        => 10,
            'name'      => 'abc123',
            'country'   => 'FR',
            'status'    => 'active',
            'deletedAt' => null,
        ];

        $this->initHalResourceProperties($props);

        $instance = new Sdk\Api\Store\StoreResource($this->halResource);

        $this->assertNull($instance->getDeletedAt());
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

    public function testGetChannelApi()
    {
        /** @var Sdk\Hal\HalResource|\PHPUnit_Framework_MockObject_MockObject $halResource */
        $halResource = $this->createMock(Sdk\Hal\HalResource::class);
        $halResource
            ->expects($this->once())
            ->method('getLink')
            ->with('channel')
            ->willReturn(
                $this->createMock(Sdk\Hal\HalLink::class)
            );

        $instance = new Sdk\Api\Store\StoreResource($halResource);

        $this->assertInstanceOf(
            Sdk\Api\Store\StoreChannelDomain::class,
            $instance->getChannelApi()
        );
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

    public function testGetPricingApi()
    {
        /** @var Sdk\Hal\HalResource|\PHPUnit_Framework_MockObject_MockObject $halResource */
        $halResource = $this->createMock(Sdk\Hal\HalResource::class);
        $halResource
            ->expects($this->once())
            ->method('getLink')
            ->with('pricing')
            ->willReturn(
                $this->createMock(Sdk\Hal\HalLink::class)
            );

        $instance = new Sdk\Api\Store\StoreResource($halResource);

        $this->assertInstanceOf(Sdk\Api\Catalog\PricingDomain::class, $instance->getPricingApi());
    }

    public function testGetTicketApi()
    {
        /** @var Sdk\Hal\HalResource|\PHPUnit_Framework_MockObject_MockObject $halResource */
        $halResource = $this->createMock(Sdk\Hal\HalResource::class);
        $halResource
            ->expects($this->once())
            ->method('getLink')
            ->with('ticket')
            ->willReturn(
                $this->createMock(Sdk\Hal\HalLink::class)
            );

        $instance = new Sdk\Api\Store\StoreResource($halResource);

        $this->assertInstanceOf(Sdk\Api\Task\TicketDomain::class, $instance->getTicketApi());
    }
}
