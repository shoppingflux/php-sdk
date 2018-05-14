<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\OrderCollection;
use ShoppingFeed\Sdk\Api\Order\OrderDomain;
use ShoppingFeed\Sdk\Hal\HalLink;
use ShoppingFeed\Sdk\Order\OrderOperation;

class OrderDomainTest extends TestCase
{
    public function testNewOrderOperation()
    {
        $link     = $this->createMock(HalLink::class);
        $instance = new OrderDomain($link);

        $this->assertEquals($instance, $instance->newOperations());
    }

    /**
     * @throws \Exception
     */
    public function testAcceptOperation()
    {
        $link            = $this->createMock(HalLink::class);
        $instance        = new OrderDomain($link);
        $orderOperations = $this->createMock(OrderOperation::class);

        $orderOperations
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                OrderOperation::TYPE_ACCEPT,
                ['reason' => 'noreason']
            );

        $instance->newOperations($orderOperations);

        $this->assertInstanceOf(
            OrderDomain::class,
            $instance->accept(
                'ref1',
                'amazon',
                'noreason'
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testCancelOperation()
    {
        $link            = $this->createMock(HalLink::class);
        $instance        = new OrderDomain($link);
        $orderOperations = $this->createMock(OrderOperation::class);

        $orderOperations
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                OrderOperation::TYPE_CANCEL,
                ['reason' => 'noreason']
            );

        $instance->newOperations($orderOperations);

        $this->assertInstanceOf(
            OrderDomain::class,
            $instance->cancel(
                'ref1',
                'amazon',
                'noreason'
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testRefuseOperation()
    {
        $link            = $this->createMock(HalLink::class);
        $instance        = new OrderDomain($link);
        $orderOperations = $this->createMock(OrderOperation::class);

        $orderOperations
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                OrderOperation::TYPE_REFUSE,
                ['refund' => ['item1', 'item2']]
            );

        $instance->newOperations($orderOperations);

        $this->assertInstanceOf(
            OrderDomain::class,
            $instance->refuse(
                'ref1',
                'amazon',
                ['item1', 'item2']
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testShipOperation()
    {
        $link            = $this->createMock(HalLink::class);
        $instance        = new OrderDomain($link);
        $orderOperations = $this->createMock(OrderOperation::class);

        $orderOperations
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                OrderOperation::TYPE_SHIP,
                [
                    'carrier'        => 'ups',
                    'trackingNumber' => '123654abc',
                    'trackingLink'   => 'http://tracking.lnk',
                ]
            );

        $instance->newOperations($orderOperations);

        $this->assertInstanceOf(
            OrderDomain::class,
            $instance->ship(
                'ref1',
                'amazon',
                'ups',
                '123654abc',
                'http://tracking.lnk'
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testExecute()
    {
        $link            = $this->createMock(HalLink::class);
        $instance        = new OrderDomain($link);
        $orderOperations = $this->createMock(OrderOperation::class);

        $orderOperations
            ->expects($this->once())
            ->method('execute')
            ->with($link)
            ->willReturn($this->createMock(OrderCollection::class));

        $instance->newOperations($orderOperations);

        $this->assertInstanceOf(
            OrderCollection::class,
            $instance->execute()
        );
    }
}
