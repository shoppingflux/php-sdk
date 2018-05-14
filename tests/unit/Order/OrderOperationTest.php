<?php
namespace ShoppingFeed\Sdk\Test\Order;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use ShoppingFeed\Sdk;

class OrderOperationTest extends TestCase
{
    private $operationCount = 10;

    /**
     * Generate operations
     *
     * @param Sdk\Order\OrderOperation $orderOperation
     *
     * @throws \Exception
     */
    private function generateOperations(Sdk\Order\OrderOperation $orderOperation)
    {
        for ($i = 0; $i < $this->operationCount; $i++) {
            $orderOperation->addOperation(
                'ref' . $i,
                'amazon',
                Sdk\Order\OrderOperation::TYPE_ACCEPT
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function testAddOperation()
    {
        $orderOperation = new Sdk\Order\OrderOperation();
        $this->generateOperations($orderOperation);

        $this->assertEquals(
            $this->operationCount,
            $orderOperation->countOperation(Sdk\Order\OrderOperation::TYPE_ACCEPT)
        );
    }

    /**
     * @throws \Exception
     */
    public function testAddWrongOperation()
    {
        $orderOperation = new Sdk\Order\OrderOperation();

        $this->expectException(\Exception::class);

        $orderOperation->addOperation(
            'ref',
            'amazon',
            'FakeType'
        );
    }

    /**
     * @throws \Exception
     */
    public function testExecute()
    {
        $link = $this->createMock(Sdk\Hal\HalLink::class);
        $link
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn(
                $this->createMock(RequestInterface::class)
            );

        /** @var Sdk\Order\OrderOperation|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(Sdk\Order\OrderOperation::class)
            ->setMethods(['getPoolSize'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('getPoolSize')
            ->willReturn(10);

        $this->generateOperations($instance);

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
                10
            );


        $this->assertInstanceOf(
            Sdk\Api\Order\OrderCollection::class,
            $instance->execute($link)
        );
    }
}
