<?php
namespace ShoppingFeed\Sdk\Test\Api;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Hal\HalResource;

abstract class AbstractResourceTest extends TestCase
{
    /**
     * @var HalResource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $halResource;

    protected $props = [];

    protected function initHalResourceProperties(array $props = [])
    {
        if (! $props) {
            $props = $this->props;
        }

        $this->halResource = $this->createMock(HalResource::class);
        $this->halResource
            ->expects($this->exactly(count($props)))
            ->method('getProperty')
            ->with($this->logicalOr(...array_keys($props)))
            ->will($this->returnCallback(function($prop) use($props) {
                return $props[$prop];
            }));

        // supports initialization by default
        $this->halResource
            ->method('get')
            ->willReturnSelf();
    }
}
