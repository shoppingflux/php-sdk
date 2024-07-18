<?php

namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use ShoppingFeed\Sdk;
use ShoppingFeed\Sdk\Api;
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

class OperationTest extends TestCase
{
    /**
     * @var Api\Order\Operation $instance
     */
    private $instance;

    /**
     * @var int $operationCount
     */
    private $operationCount = 10;

    /**
     * @var Id
     */
    private $identifier;

    public function setUp(): void
    {
        $this->identifier = new Id(123);
        $this->instance   = new Api\Order\Operation();
    }

    public function testAddOperations(): void
    {
        $this->instance->refuse($this->identifier);
        $this->instance->deliver($this->identifier);
        $this->instance->accept($this->identifier, 'reason');
        $this->instance->cancel($this->identifier, 'reason');
        $this->instance->ship($this->identifier, 'ups', '123654abc', 'http://tracking.lnk');
        $this->instance->refund(
            $this->identifier,
            true,
            [
                ['reference' => 'item1', 'quantity' => 1],
                ['reference' => 'item2', 'quantity' => 2],
            ]
        );
        $this->instance->uploadDocument(
            $this->identifier,
            new Api\Order\Document\Invoice('/tmp/ref1_amazon_invoice.pdf')
        );
        $this->instance->acknowledge($this->identifier, '123654abc', 'success', 'message');
        $this->instance->unacknowledge($this->identifier);

        $this->assertEquals(1, $this->instance->count(Api\Order\Operation::TYPE_REFUSE));
        $this->assertEquals(9, $this->instance->count());
    }

    public function testExecute(): void
    {
        $this->generateOperations();

        $link = $this->createMock(Sdk\Hal\HalLink::class);
        $link
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn($this->createMock(RequestInterface::class));
        $link
            ->expects($this->once())
            ->method('batchSend')
            ->with(
                [$this->createMock(RequestInterface::class)],
                function (Sdk\Hal\HalResource $resource) use (&$resources) {
                    array_push($resources, ...$resource->getResources('order'));
                },
                null,
                [],
                $this->operationCount
            );

        $this->assertInstanceOf(Api\Order\OrderOperationResult::class, $this->instance->execute($link));
    }

    private function generateOperations(): void
    {
        for ($i = 0; $i < $this->operationCount; $i++) {
            $this->instance->accept($this->identifier);
        }
    }
}
