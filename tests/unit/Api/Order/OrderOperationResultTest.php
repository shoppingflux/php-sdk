<?php

namespace ShoppingFeed\Sdk\Test\Api\Order;

use ShoppingFeed\Sdk\Api\Order\OrderOperationBatch;
use ShoppingFeed\Sdk\Api\Order\OrderOperationResponse;
use ShoppingFeed\Sdk\Api\Order\OrderOperationResult;
use ShoppingFeed\Sdk\Hal\HalResource;

class OrderOperationResultTest extends OrderOperationTestCase
{
    private array $resources = [];
    private OrderOperationResult $instance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resources = [
            'a' => $this->createResourceMock('a', 1, 'REF123', $this->link),
            'b' => $this->createResourceMock('b', 2, 'REF456', $this->link),
        ];

        $this->instance = new OrderOperationResult($this->resources);
    }

    protected function getInstance(): OrderOperationResponse|OrderOperationResult
    {
        return $this->instance;
    }

    protected function getWaitCallCount(): int
    {
        return 2;
    }

    public function testBatchIds(): void
    {
        $this->assertSame(['a', 'b'], $this->instance->getBatchIds());
    }

    public function testGetBatches(): void
    {
        $batches = $this->instance->getBatches();

        $this->assertCount(2, $batches);

        foreach ($batches as $batch) {
            $this->assertInstanceOf(OrderOperationBatch::class, $batch);
        }
    }
}
