<?php
namespace ShoppingFeed\Sdk\Test\Api\Catalog;

use ShoppingFeed\Sdk;

class InventoryResourceTest extends Sdk\Test\Api\AbstractResourceTest
{
    public function setUp()
    {
        $this->props = [
            'id'        => 10,
            'reference' => 'abc123',
            'quantity'  => 15,
            'updatedAt' => 'now',
        ];
    }

    public function testPropertiesGetters()
    {
        $this->initPropertyGetterTester();

        $instance = new Sdk\Api\Catalog\InventoryResource($this->propertyGetter);

        $this->assertEquals($this->props['id'], $instance->getId());
        $this->assertEquals($this->props['reference'], $instance->getReference());
        $this->assertEquals($this->props['quantity'], $instance->getQuantity());
        $this->assertEquals(new \DateTimeImmutable($this->props['updatedAt']), $instance->getUpdatedAt());
    }
}
