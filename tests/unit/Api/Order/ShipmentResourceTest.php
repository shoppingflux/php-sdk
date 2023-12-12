<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use ShoppingFeed\Sdk;

class ShipmentResourceTest extends Sdk\Test\Api\AbstractResourceTest
{
    public function testPropertiesGetters(): void
    {
        $this->initHalResourceProperties([
            'items'          => null,
            'carrier'        => 'ES_CORREOS',
            'trackingNumber' => 'PQ9P3N0710096540184000K',
            'trackingLink'   => 'https://url.test/link',
            'returnInfo'     => [
                'carrier'        => null,
                'trackingNumber' => null,
            ],
            'createdAt'      => '2023-11-28T14:25:18+00:00',
        ]);

        $instance = new Sdk\Api\Order\ShipmentResource($this->halResource);

        $this->assertNull($instance->getItems());
        $this->assertEquals('ES_CORREOS', $instance->getCarrier());
        $this->assertEquals('PQ9P3N0710096540184000K', $instance->getTrackingNumber());
        $this->assertEquals('https://url.test/link', $instance->getTrackingUrl());
        $this->assertEquals(['carrier' => null, 'trackingNumber' => null], $instance->getReturnInfo());
        $this->assertEquals(new \DateTimeImmutable('2023-11-28T14:25:18+00:00'), $instance->getCreatedAt());
    }
}
