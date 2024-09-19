<?php

namespace ShoppingFeed\Sdk\Api\Order;

class OrderItemCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @param array $items  Collection of untyped order items
     *
     * @return OrderItemCollection  A new instance of self, holding relevant items
     */
    public static function fromProperties(array $items)
    {
        $instance = new self;
        foreach ($items as $item) {
            $instance->add(
                new OrderItem(
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
                )
            );
        }

        return $instance;
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return \ArrayIterator|OrderItem[]
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function(OrderItem $item) {
            return $item->toArray();
        }, $this->items);
    }

    /**
     * @param OrderItem $item
     */
    private function add(OrderItem $item)
    {
        $this->items[] = $item;
    }
}
