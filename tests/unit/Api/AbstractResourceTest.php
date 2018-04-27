<?php
namespace ShoppingFeed\Sdk\Test\Api;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Hal\HalResource;

abstract class AbstractResourceTest extends TestCase
{
    /**
     * @var HalResource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $propertyGetter;

    protected $props = [];

    protected function initPropertyGetterTester()
    {
        $this->propertyGetter = $this->createMock(HalResource::class);
        $this->propertyGetter
            ->expects($this->exactly(count($this->props)))
            ->method('getProperty')
            ->with($this->logicalOr(...array_keys($this->props)))
            ->will($this->returnCallback([$this, 'get']));
    }

    /**
     * Simulate getProperty return
     */
    public function get($prop)
    {
        return $this->props[$prop];
    }
}
