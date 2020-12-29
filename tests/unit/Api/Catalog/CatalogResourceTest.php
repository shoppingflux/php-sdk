<?php
namespace ShoppingFeed\Sdk\Test\Api\Catalog;

use ShoppingFeed\Sdk;

class CatalogResourceTest extends Sdk\Test\Api\AbstractResourceTest
{
    public function setUp()
    {
        $this->props = [
            'id'        => 10,
            '_links' => [
                'operation' => 'abc/123'
            ],
        ];
    }

    public function testPropertiesGetters()
    {
        $this->initHalResourceProperties();

        $this->halResource
            ->expects($this->once())
            ->method('getLink')
            ->with('operation')
            ->willReturn('abc/123');

        $instance = new Sdk\Api\Catalog\CatalogResource($this->halResource);

        $this->assertSame($this->props['id'], $instance->getId());
        $this->assertSame($this->props['_links']['operation'], $instance->getOperationLink());
    }
}
