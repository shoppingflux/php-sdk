<?php
namespace ShoppingFeed\Sdk\Test\Api\Task;

use ShoppingFeed\Sdk\Api\Task\TicketCollection;
use ShoppingFeed\Sdk\Api\Task\TicketPaginatedCollection;
use ShoppingFeed\Sdk\Api\Task\TicketResource;
use ShoppingFeed\Sdk\Hal\HalResource;
use ShoppingFeed\Sdk\Test\Api\AbstractResourceTest;
use ShoppingFeed\Sdk\Test\Resource\ResourceMock;

class TicketPaginatedCollectionTest extends AbstractResourceTest
{
    public function setUp()
    {
        $this->props = [
            'id' => '123abc',
        ];
    }

    public function testGetproperty()
    {
        $this->initHalResourceProperties();

        $instance = new TicketPaginatedCollection($this->halResource, TicketResource::class);

        $this->assertEquals($this->props['id'], $instance->getId());
    }

    public function testIsBeingProcessing()
    {
        /** @var TicketPaginatedCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(TicketPaginatedCollection::class)
            ->setConstructorArgs(
                [
                    $this->createMock(HalResource::class),
                    ResourceMock::class,
                ]
            )
            ->setMethods(['getProperty'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('getProperty')
            ->with('meta')
            ->willReturn(['processing' => true]);

        $this->assertTrue($instance->isBeingProcessed());
    }

    public function testIsBeingProcessingDefaultBehaviour()
    {
        /** @var TicketPaginatedCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(TicketPaginatedCollection::class)
            ->setConstructorArgs(
                [
                    $this->createMock(HalResource::class),
                    ResourceMock::class,
                ]
            )
            ->setMethods(['getProperty'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('getProperty')
            ->with('meta')
            ->willReturn([]);

        $this->assertFalse($instance->isBeingProcessed());
    }
}
