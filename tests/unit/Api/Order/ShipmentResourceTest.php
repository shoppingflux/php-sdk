<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use ShoppingFeed\Sdk;

class ShipmentResourceTest extends Sdk\Test\Api\AbstractResourceTest
{
    public function setUp(): void
    {
        $this->props = [
            'items' => null,
            'carrier' => 'ES_CORREOS',
            'trackingNumber' => 'PQ9P3N0710096540184000K',
            'trackingLink' => 'https://url.test/link',
            'returnInfo' =>[
                'carrier' => null,
                'trackingNumber' => null,
            ],
            'createdAt' => '2023-11-28T14:25:18+00:00',
        ];
    }


    public function testPropertiesGetters()
    {
        $this->initHalResourceProperties();

        $instance = new Sdk\Api\Order\ShipmentResource($this->halResource);

        $this->assertNull($instance->getItems());
        $this->assertEquals($this->props['carrier'], $instance->getCarrier());
        $this->assertEquals($this->props['trackingNumber'], $instance->getTrackingNumber());
        $this->assertEquals($this->props['trackingLink'], $instance->getTrackingUrl());
        $this->assertEquals($this->props['returnInfo'], $instance->getReturnInfo());
        $this->assertEquals(date_create_immutable($this->props['createdAt']), $instance->getCreatedAt());
    }
}
