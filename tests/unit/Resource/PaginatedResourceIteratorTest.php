<?php

namespace ShoppingFeed\Sdk\Resource;

use PHPUnit\Framework\TestCase;

class PaginatedResourceIteratorTest extends TestCase
{
    /**
     * @var PaginatedResourceIterator
     */
    private $instance;

    /**
     * @var PaginatedResourceCollection
     */
    private $collection;

    public function setUp(): void
    {
        $this->collection = $this->createMock(PaginatedResourceCollection::class);
        $this->instance   = new PaginatedResourceIterator($this->collection);
    }

    public function testGetTotalCount()
    {
        $this->collection
            ->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(100);

        $this->assertSame(100, $this->instance->count());
    }

    public function testGetMeta()
    {
        $this->collection
            ->expects($this->once())
            ->method('getMeta')
            ->with('test')
            ->willReturn('toto');

        $this->assertSame('toto', $this->instance->getMeta('test'));
    }

    public function testGetIteratorLoopOverInnerCollection()
    {
        $this->collection
            ->expects($this->exactly(2))
            ->method('next')
            ->willReturnOnConsecutiveCalls($this->collection, null);

        $this->collection
            ->expects($this->exactly(2))
            ->method('getIterator')
            ->willReturn(new \ArrayIterator(['a']));

        $result = iterator_to_array($this->instance->getIterator());
        $this->assertSame(['a', 'a'], $result);
    }
}

