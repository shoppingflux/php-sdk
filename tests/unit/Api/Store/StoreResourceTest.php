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
        $this->initPropertyGetterTester();

        $instance = new Sdk\Api\Store\StoreResource($this->propertyGetter);

        $this->assertEquals($this->props['id'], $instance->getId());
        $this->assertEquals($this->props['name'], $instance->getName());
        $this->assertEquals($this->props['country'], $instance->getCountryCode());
        $this->assertTrue($instance->isActive());
    }
}
