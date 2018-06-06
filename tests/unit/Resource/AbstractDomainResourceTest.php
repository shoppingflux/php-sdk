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

    public function testGetPagesAndCreatePaginator()
    {
        // $i is used to generate the awaited level of depth
        $i          = 0;
        $pageFrom   = 1;
        $perPage    = 20;
        $totalItems = 400;

        $link = $this->createMock(HalLink::class);
        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->getPaginatedResource($i, $pageFrom, $perPage, $totalItems));

        $instance = new DomainResourceMock($link);

        $count     = 0;
        $criterias = ['page' => $pageFrom, 'limit' => $perPage];
        foreach ($instance->getPages($criterias) as $collection) {
            $count++;
            $this->assertInstanceOf(PaginatedResourceCollection::class, $collection);
        }

        // NumberItem / ItemPerPage = NumberOfPages,
        // NumberOfPages - (PageToStartAt - 1) = AwaitedNumberOfPages (-1 because page 0 does no exists)
        $this->assertEquals(($totalItems / $perPage) - ($pageFrom - 1), $count);
    }

    public function testGetAll()
    {
        $filters = ['attr1' => 'value1'];
        $pages   = [
            [
                $this->createMock(HalResource::class),
                $this->createMock(HalResource::class),
                $this->createMock(HalResource::class),
                $this->createMock(HalResource::class),
            ],
            [
                $this->createMock(HalResource::class),
                $this->createMock(HalResource::class),
                $this->createMock(HalResource::class),
                $this->createMock(HalResource::class),
            ],
        ];
        $link    = $this->createMock(HalLink::class);

        /** @var DomainResourceMock|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(DomainResourceMock::class)
            ->setConstructorArgs([$link])
            ->setMethods(['getPages'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('getPages')
            ->with(
                ['filters' => $filters])
            ->will($this->returnCallback(
                function ($criterias) use ($pages) {
                    foreach ($pages as $page) {
                        yield $page;
                    }
                }
            ));

        $count = 0;
        foreach ($instance->getAll($filters) as $resource) {
            $count++;
        }

        $awaited = 0;
        foreach ($pages as $page) {
            $awaited += count($page);
        }

        $this->assertEquals($awaited, $count);
    }

    /**
     * Recurse to simulate load of next link
     *
     * @param int &$i
     * @param int $pageFrom
     * @param int $perPage
     * @param int $totalItems
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPaginatedResource(&$i = 0, $pageFrom = 1, $perPage = 10, $totalItems = 400)
    {
        $i += $perPage;

        $resource = $this
            ->getMockBuilder(HalResource::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLink'])
            ->getMock();

        $link = $this
            ->getMockBuilder(HalLink::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn(
                $i < $totalItems ? $this->getPaginatedResource($i, $pageFrom, $perPage) : null
            );

        $resource
            ->expects($this->once())
            ->method('getLink')
            ->with('next')
            ->willReturn($link);

        return $resource;
    }
}
