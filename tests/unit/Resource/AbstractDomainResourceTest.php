<?php
namespace ShoppingFeed\Sdk\Test\Resource;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Hal\HalLink;
use ShoppingFeed\Sdk\Hal\HalResource;
use ShoppingFeed\Sdk\Resource\PaginatedResourceCollection;

class AbstractDomainResourceTest extends TestCase
{
    public function testGetPageNullOnDeadLink()
    {
        $link = $this->createMock(HalLink::class);
        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $instance = new DomainResourceMock($link);

        $this->assertNull($instance->getPage());
    }

    public function testGetPage()
    {
        $link = $this->createMock(HalLink::class);
        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->createMock(HalResource::class));

        $instance = new DomainResourceMock($link);

        $this->assertInstanceOf(PaginatedResourceCollection::class, $instance->getPage());
    }
}
