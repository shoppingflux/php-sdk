<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use ShoppingFeed\Sdk;
use ShoppingFeed\Sdk\Hal\HalLink;
use ShoppingFeed\Sdk\Hal\HalResource;
use ShoppingFeed\Sdk\Resource\PaginatedResourceIterator;

class OrderResourceTest extends Sdk\Test\Api\AbstractResourceTest
{
    public function setUp(): void
    {
        $this->props = [
            'id'                     => 10,
            'reference'              => 'abc123',
            'storeReference'         => 'def456',
            'status'                 => 'active',
            'createdAt'              => '2017-12-05',
            'updatedAt'              => '2017-12-06',
            'acknowledgedAt'         => '2017-12-07',
            'payment'                => [
                "carrier"        => "Home",
                "trackingNumber" => "94718832",
            ],
            'shipment'               => [
                "shippingAmount" => 58.8,
                "productAmount"  => 495.24,
                "totalAmount"    => 554.04,
                "currency"       => "EUR",
                "method"         => "",
            ],
            'shippingAddress'        => [
                "firstName"         => "Bill",
                "lastName"          => "BOQUET",
                "company"           => "BD",
                "street"            => "10 RUE RUE DE BOULE",
                "additionalDetails" => "",
                "postalCode"        => "75000",
                "city"              => "PARIS",
                "country"           => "FR",
                "phone"             => "061234579",
                "email"             => "biletboule@mail.com",
            ],
            'billingAddress'         => [
                "firstName"         => "Bill",
                "lastName"          => "BOQUET",
                "company"           => "BD",
                "street"            => "10 RUE RUE DE BOULE",
                "additionalDetails" => "",
                "postalCode"        => "75000",
                "city"              => "PARIS",
                "country"           => "FR",
                "phone"             => "061234579",
                "email"             => "biletboule@mail.com",
            ],
            'itemsReferencesAliases' => [
                'item1' => 'alias1',
                'item2' => 'alias2',
            ],
        ];

    }

    public function testPropertiesGetters()
    {
        $this->initHalResourceProperties();

        $instance = new Sdk\Api\Order\OrderResource($this->halResource);

        $this->assertEquals($this->props['id'], $instance->getId());
        $this->assertEquals($this->props['reference'], $instance->getReference());
        $this->assertEquals($this->props['storeReference'], $instance->getStoreReference());
        $this->assertEquals($this->props['status'], $instance->getStatus());
        $this->assertEquals($this->props['payment'], $instance->getPaymentInformation());
        $this->assertEquals($this->props['shipment'], $instance->getShipment());
        $this->assertEquals($this->props['shippingAddress'], $instance->getShippingAddress());
        $this->assertEquals($this->props['billingAddress'], $instance->getBillingAddress());
        $this->assertEquals($this->props['itemsReferencesAliases'], $instance->getItemsReferencesAliases());
        $this->assertEquals(date_create_immutable($this->props['createdAt']), $instance->getCreatedAt());
        $this->assertEquals(date_create_immutable($this->props['updatedAt']), $instance->getUpdatedAt());
        $this->assertEquals(date_create_immutable($this->props['acknowledgedAt']), $instance->getAcknowledgedAt());
    }

    public function testNullDates()
    {
        $this->props = [
            'updatedAt'      => null,
            'acknowledgedAt' => null,
        ];
        $this->initHalResourceProperties();

        $instance = new Sdk\Api\Order\OrderResource($this->halResource);

        $this->assertNull($instance->getUpdatedAt());
        $this->assertNull($instance->getAcknowledgedAt());
    }

    public function testGetItemsLoadOrderEntity()
    {
        $this->initHalResourceProperties([
            'items' => [
                [
                    'id'               => 123,
                    'reference'        => 'a',
                    'status'           => '',
                    'quantity'         => 1,
                    'price'            => 9.99,
                    'commission'       => null,
                    'taxAmount'        => 7.99,
                    'ecotaxAmount'     => 0,
                    'channelReference' => 'ref',
                    'additionalFields' => ['taxRate' => 20],
                    'name'             => null,
                    'image'            => null,
                ],
            ],
        ]);

        $instance = new Sdk\Api\Order\OrderResource($this->halResource);
        $items    = $instance->getItems();

        $this->assertInstanceOf(Sdk\Api\Order\OrderItemCollection::class, $items);
        $this->assertCount(1, $items, 'item is in collection');
    }

    public function testGetChannel()
    {
        $resource = $this->createMock(Sdk\Hal\HalResource::class);
        $response = $this->createMock(Sdk\Hal\HalResource::class);
        $response
            ->expects($this->once())
            ->method('getFirstResource')
            ->with('channel')
            ->willReturn($resource);

        $instance = new Sdk\Api\Order\OrderResource($response);

        $channel = $instance->getChannel();

        $this->assertInstanceOf(Sdk\Api\Channel\ChannelResource::class, $channel);
    }

    public function testGetShipments(): void
    {
        $resource = $this->initHalResourceProperties();
        $instance = new Sdk\Api\Order\OrderResource($this->halResource);

        $resource
            ->expects($this->any())
            ->method('getLink')
            ->with('self')
            ->willReturn($link = $this->createMock(HalLink::class));

        $link
            ->expects($this->once())
            ->method('withAddedHref')
            ->with('/shipment')
            ->willReturn($link);

        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->createMock(HalResource::class));

        $this->assertInstanceOf(PaginatedResourceIterator::class, $instance->getShipments());
    }
}
