<?php

namespace ShoppingFeed\Sdk\Api\Order;

use PHPUnit\Framework\TestCase;

class OrderItemCollectionTest extends TestCase
{
    private $items = [
        [
            'id'               => 123,
            'reference'        => 'a',
            'status'           => '',
            'quantity'         => 1,
            'price'            => 2,
            'commission'       => null,
            'taxAmount'        => 7.99,
            'ecotaxAmount'     => 0,
            'channelReference' => 'ref',
            'additionalFields' => ['taxRate' => 20],
            'name'             => null,
            'image'            => null,
        ],
        [
            'id'               => 456,
            'reference'        => 'b',
            'status'           => '',
            'quantity'         => 2,
            'price'            => 3,
            'commission'       => null,
            'taxAmount'        => 4.99,
            'ecotaxAmount'     => 0,
            'channelReference' => 'ref2',
            'additionalFields' => ['taxRate' => 45],
            'name'             => null,
            'image'            => null,
        ],
    ];

    public function testCreateCollectionFromArrayOfRemoteProperties()
    {
        $instance = OrderItemCollection::fromProperties($this->items);

        self::assertInstanceOf(OrderItemCollection::class, $instance);
        $this->assertCount(2, $instance);

        $items = $instance->getIterator()->getArrayCopy();
        $this->assertContainsOnlyInstancesOf(OrderItem::class, $items);
    }

    public function testItemsAreWellConstructedFromArray()
    {
        $instance = OrderItemCollection::fromProperties($this->items);
        $items    = $instance->getIterator()->getArrayCopy();

        $firstOne  = $this->createItem($this->items[0]);
        $secondOne = $this->createItem($this->items[1]);

        $this->assertEquals($items[0], $firstOne);
        $this->assertEquals($items[1], $secondOne);
    }

    private function createItem(array $item): OrderItem
    {
        return new OrderItem(
            $item['id'],
            $item['reference'],
            $item['status'],
            $item['quantity'],
            $item['price'],
            $item['commission'],
            $item['taxAmount'],
            $item['ecotaxAmount'],
            $item['channelReference'],
            $item['additionalFields'],
            $item['name'],
            $item['image']
        );
    }

    public function testCollectionCanBeRevertedBackToArray()
    {
        $instance = OrderItemCollection::fromProperties($this->items);
        $this->assertEquals($this->items, $instance->toArray());
    }
}