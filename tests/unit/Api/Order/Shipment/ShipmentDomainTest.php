<?php

namespace ShoppingFeed\Sdk\Test\Api\Order\Shipment;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\Shipment\ShipmentDomain;
use ShoppingFeed\Sdk\Api\Order\Shipment\ShipmentResource;
use ShoppingFeed\Sdk\Hal\HalLink;
use ShoppingFeed\Sdk\Hal\HalResource;

class ShipmentDomainTest extends TestCase
{
    /**
     * @dataProvider getProvidedData
     */
    public function testShipmentGetters($resources, $expected): void
    {
        $link = $this->createMock(HalLink::class);

        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn($response = $this->createMock(HalResource::class));

        $response
            ->expects($this->once())
            ->method('getAllResources')
            ->willReturn($resources);

        $instance = new ShipmentDomain($link);

        $this->assertEquals($expected, $instance->getAll());
    }

    public function getProvidedData(): array
    {
        return [
            'with_shipments'    => [
                [
                    [
                        ($resource1 = $this->createMock(HalResource::class)),
                        ($resource2 = $this->createMock(HalResource::class))
                    ]
                ],
                [new ShipmentResource($resource1), new ShipmentResource($resource2)]
            ],
            'without_shipments' => [[[]], []],
        ];
    }
}
