<?php
namespace ShoppingFeed\Sdk\Test\Api\Catalog;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Catalog\InventoryResource;
use ShoppingFeed\Sdk\Hal\HalResource;

class InventoryResourceTest extends TestCase
{
    /**
     * @var array
     */
    private $props = [];

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
        $halResource = $this->createMock(HalResource::class);
        $halResource
            ->expects($this->exactly(count($this->props)))
            ->method('getProperty')
            ->with($this->logicalOr(...array_keys($this->props)))
            ->will($this->returnCallback([$this, 'returnGetters']));

        $instance = new InventoryResource($halResource);

        $this->assertEquals($this->props['id'], $instance->getId());
        $this->assertEquals($this->props['reference'], $instance->getReference());
        $this->assertEquals($this->props['quantity'], $instance->getQuantity());
        $this->assertEquals(new \DateTimeImmutable($this->props['updatedAt']), $instance->getUpdatedAt());
    }

    /**
     * Simulate getProperty return
     */
    public function returnGetters($prop)
    {
        return $this->props[$prop];
    }
}
