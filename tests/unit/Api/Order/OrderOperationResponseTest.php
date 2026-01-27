<?php

namespace ShoppingFeed\Sdk\Test\Api\Order;

use ShoppingFeed\Sdk\Api\Order\OrderOperationResponse;
use ShoppingFeed\Sdk\Api\Order\OrderOperationResult;
use ShoppingFeed\Sdk\Hal\HalResource;

class OrderOperationResponseTest extends OrderOperationTestCase
{
    private HalResource $resource;
    private OrderOperationResponse $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResourceMock('a', 1, 'REF123', $this->link);
        $this->response = new OrderOperationResponse($this->resource);
    }

    protected function getInstance(): OrderOperationResponse|OrderOperationResult
    {
        return $this->response;
    }

    protected function getWaitCallCount(): int
    {
        return 1;
    }

    public function testBatchId(): void
    {
        $this->assertSame('a', $this->response->getBatchId());
    }

    public function testGetReport(): void
    {
        $expectedReport = [
            [
                'id'          => 1,
                'channelName' => 'Channel A',
                'reference'   => 'REF123',
                'state'       => 'success',
                'message'     => 'Order processed successfully.'
            ]
        ];

        $this->assertSame($expectedReport, $this->response->getReport());
    }

    public function testExceptionOnMissingTicketLink(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Ticket link is missing from the OrderOperationResponse resource.');

        $resource = $this->createResourceMock('a', 1, 'REF123');
        new OrderOperationResponse($resource);
    }
}
