<?php

namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\OrderOperationResponse;
use ShoppingFeed\Sdk\Api\Order\OrderOperationResult;
use ShoppingFeed\Sdk\Api\Task\TicketResource;
use ShoppingFeed\Sdk\Hal\HalLink;
use ShoppingFeed\Sdk\Hal\HalResource;

abstract class OrderOperationTestCase extends TestCase
{
    protected $link;

    protected function setUp(): void
    {
        $this->link = $this->createMock(HalLink::class);
    }

    protected function createResourceMock($batchId, $orderId, $reference, $link = null)
    {
        $resource = $this->createMock(HalResource::class);

        $resource
            ->method('getLink')
            ->with('ticket')
            ->willReturn($link);

        $callCount = 0;
        $resource
            ->method('getProperty')
            ->willReturnCallback(function () use (&$callCount, $batchId, $orderId, $reference) {
                $callCount++;
                return match ($callCount) {
                    1, 3    => $batchId,
                    2, 4    => [
                        [
                            'id'          => $orderId,
                            'channelName' => 'Channel A',
                            'reference'   => $reference,
                            'state'       => 'success',
                            'message'     => 'Order processed successfully.'
                        ]
                    ],
                    default => null,
                };
            });

        return $resource;
    }

    abstract protected function getInstance(): OrderOperationResponse|OrderOperationResult;

    abstract protected function getWaitCallCount(): int;

    public function testWait(): void
    {
        $this->link
            ->expects($this->exactly($this->getWaitCallCount()))
            ->method('get')
            ->willReturn($this->createMock(HalResource::class));

        $this->assertSame($this->getInstance(), $this->getInstance()->wait(1));
    }

    public function testGetTickets(): void
    {
        $resource = $this->createMock(HalResource::class);
        $this->link
            ->expects($this->exactly($this->getWaitCallCount()))
            ->method('get')
            ->willReturn($resource);

        $resource
            ->method('getAllResources')
            ->willReturn([$resource]);

        $this->assertContainsOnly(
            TicketResource::class,
            $this->getInstance()->getTickets()
        );
    }
}
