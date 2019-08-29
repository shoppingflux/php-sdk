<?php
namespace ShoppingFeed\Sdk\Test\Api\Task;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk;

class TicketDomainTest extends TestCase
{
    public function testGetByReference()
    {
        $reference = 'abc213';
        $link      = $this->createMock(Sdk\Hal\HalLink::class);
        $resource  = $this->createMock(Sdk\Hal\HalResource::class);
        $resource
            ->expects($this->once())
            ->method('getProperty')
            ->with('count')
            ->willReturn(10);
        $resource
            ->expects($this->once())
            ->method('getFirstResource')
            ->with('ticket')
            ->willReturn(
                $this->createMock(Sdk\Hal\HalResource::class)
            );
        $link
            ->expects($this->once())
            ->method('get')
            ->with(
                [],
                ['query' => ['reference' => $reference]]
            )
            ->willReturn($resource);

        $instance = new Sdk\Api\Task\TicketDomain($link);

        $this->assertInstanceOf(Sdk\Api\Task\TicketResource::class, $instance->getByReference($reference));
    }
    public function testGetByBatch()
    {
        $reference = 'abc213';
        $link      = $this->createMock(Sdk\Hal\HalLink::class);
        $resource  = $this->createMock(Sdk\Hal\HalResource::class);
        $resource
            ->expects($this->once())
            ->method('getProperty')
            ->with('count')
            ->willReturn(10);
        $link
            ->expects($this->once())
            ->method('get')
            ->with(
                [],
                ['query' => ['batchId' => $reference]]
            )
            ->willReturn($resource);

        $instance = new Sdk\Api\Task\TicketDomain($link);

        $this->assertInstanceOf(
            Sdk\Api\Task\TicketCollection::class,
            $instance->getByBatch($reference)
        );
    }
}
