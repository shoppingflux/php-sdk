<?php

namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api;
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;
use ShoppingFeed\Sdk\Api\Order\Identifier\Reference;

class OperationTest extends TestCase
{
    /**
     * @var Api\Order\Operation $instance
     */
    private $instance;

    public function setUp(): void
    {
        $this->instance = $this
            ->getMockBuilder(Api\Order\Operation::class)
            ->setMethods(['addOperation'])
            ->getMock();
    }

    /**
     * @dataProvider operationProvider
     */
    public function testOperationWithId(string $operation, ...$args): void
    {
        $id = new Id(123);

        $this->instance->expects($this->once())->method('addOperation')->with($id, ...$args);

        $this->assertInstanceOf(Api\Order\Operation::class, $this->instance->$operation($id));
    }

    /**
     * @dataProvider operationProvider
     */
    public function testDeliverOperationWithReferenceAndChannelName(string $operation, ...$args): void
    {
        $reference = new Reference('ref1', 'amazon');

        $this->instance->expects($this->once())->method('addOperation')->with($reference, ...$args);

        $this->assertInstanceOf(Api\Order\Operation::class, $this->instance->$operation($reference));
    }

    public function operationProvider(): array
    {
        return [
            'accept'  => [
                'accept',
                Api\Order\Operation::TYPE_ACCEPT,
                ['reason' => ''],
            ],
            'refuse' => [
                'refuse',
                Api\Order\Operation::TYPE_REFUSE,
            ],
            'ship' => [
                'ship',
                Api\Order\Operation::TYPE_SHIP,
                [
                    'carrier'        => '',
                    'trackingNumber' => '',
                    'trackingLink'   => '',
                    'items'          => [],
                ],
            ],
            'cancel' => [
                'cancel',
                Api\Order\Operation::TYPE_CANCEL,
                ['reason' => ''],
            ],
            'refund' => [
                'refund',
                Api\Order\Operation::TYPE_REFUND,
                ['refund' => ['shipping' => true, 'products' => []]],
            ],
            'acknowledge' => [
                'acknowledge',
                Api\Order\Operation::TYPE_ACKNOWLEDGE,
            ],
            'unacknowledge' => [
                'unacknowledge',
                Api\Order\Operation::TYPE_UNACKNOWLEDGE,
            ],
            'deliver' => [
                'deliver',
                Api\Order\Operation::TYPE_DELIVER,
            ],
        ];
    }
}
