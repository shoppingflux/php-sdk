<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\OperationBatchCollection;
use ShoppingFeed\Sdk\Api\Task\BatchResource;
use ShoppingFeed\Sdk\Order\Exception\BatchNotFoundException;
use ShoppingFeed\Sdk\Api\Order\OrderOperation;

class OperationBatchCollectionTest extends TestCase
{
    /**
     * @var array
     */
    private $tickets = [];

    /**
     * @var array
     */
    private $data = [
        OrderOperation::TYPE_ACCEPT      => [
            'ticketId123' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId456' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_CANCEL      => [
            'ticketId789' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId321' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_SHIP        => [
            'ticketId654' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId987' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_REFUSE      => [
            'ticketId159' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId753' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_ACKNOWLEDGE => [
            'ticketId147' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId258' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_REFUND => [
            'ticketId239' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId446' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
    ];

    public function testFindTicket()
    {
        $this->generateTickets();
        $instance = new OperationBatchCollectionMock($this->tickets, $this->data);
        $tickets  = $instance->findBatchs(['reference' => 'orderRef5', 'operation' => OrderOperation::TYPE_SHIP]);

        $this->assertInstanceOf(BatchResource::class, $tickets[0]);
        $this->assertEquals('ticketId987', $tickets[0]->getId());
    }

    public function testFindTicketWithNoOperationNoRef()
    {
        $this->generateTickets();
        $instance = new OperationBatchCollectionMock($this->tickets, $this->data);
        $tickets  = $instance->findBatchs();

        $this->assertCount(count($this->tickets), $tickets);
    }

    public function testFindTicketForOperation()
    {
        $this->generateTickets();
        $instance = new OperationBatchCollectionMock($this->tickets, $this->data);
        $tickets  = $instance->findBatchs(['operation' => OrderOperation::TYPE_SHIP]);

        $this->assertCount(count($this->data[OrderOperation::TYPE_SHIP]), $tickets);
    }

    public function testGetShippedTicket()
    {
        /** @var OperationBatchCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OperationBatchCollection::class)
            ->setMethods(['findBatchs'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatchs')
            ->with(['reference' => 'orderRef5', 'operation' => OrderOperation::TYPE_SHIP])
            ->willReturn(
                $this->createMock(BatchResource::class)
            );

        $instance->getShipped('orderRef5');
    }

    public function testGetCanceledTicket()
    {
        /** @var OperationBatchCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OperationBatchCollection::class)
            ->setMethods(['findBatchs'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatchs')
            ->with(['reference' => 'orderRef4', 'operation' => OrderOperation::TYPE_CANCEL])
            ->willReturn(
                $this->createMock(BatchResource::class)
            );

        $instance->getCanceled('orderRef4');
    }

    public function testGetRefusedTicket()
    {
        /** @var OperationBatchCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OperationBatchCollection::class)
            ->setMethods(['findBatchs'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatchs')
            ->with(['reference' => 'orderRef1', 'operation' => OrderOperation::TYPE_REFUSE])
            ->willReturn(
                $this->createMock(BatchResource::class)
            );

        $instance->getRefused('orderRef1');
    }

    public function testGetRefundedTicket()
    {
        /** @var OperationBatchCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OperationBatchCollection::class)
            ->setMethods(['findBatchs'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatchs')
            ->with(['reference' => 'orderRef1', 'operation' => OrderOperation::TYPE_REFUND])
            ->willReturn(
                $this->createMock(BatchResource::class)
            );

        $instance->getRefunded('orderRef1');
    }

    public function testGetAcceptedTicket()
    {
        /** @var OperationBatchCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OperationBatchCollection::class)
            ->setMethods(['findBatchs'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatchs')
            ->with(['reference' => 'orderRef3', 'operation' => OrderOperation::TYPE_ACCEPT])
            ->willReturn(
                $this->createMock(BatchResource::class)
            );

        $instance->getAccepted('orderRef3');
    }

    public function testGetAcknowledgeTicket()
    {
        /** @var OperationBatchCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OperationBatchCollection::class)
            ->setMethods(['findBatchs'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatchs')
            ->with(['reference' => 'orderRef6', 'operation' => OrderOperation::TYPE_ACKNOWLEDGE])
            ->willReturn(
                $this->createMock(BatchResource::class)
            );

        $instance->getAcknowledge('orderRef6');
    }

    public function testGetUnacknowledgeTicket()
    {
        /** @var OperationBatchCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OperationBatchCollection::class)
            ->setMethods(['findBatchs'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatchs')
            ->with(['reference' => 'orderRef7', 'operation' => OrderOperation::TYPE_UNACKNOWLEDGE])
            ->willReturn(
                $this->createMock(BatchResource::class)
            );

        $instance->getUnacknowledge('orderRef7');
    }

    public function testFindTicketWrongOperation()
    {
        $this->generateTickets();
        $instance = new OperationBatchCollection($this->tickets);

        $this->assertEquals([], $instance->getShipped('orderRef5'));
    }

    public function testFindTicketWrongReference()
    {
        $this->generateWrongTickets();
        $instance = new OperationBatchCollection($this->tickets, $this->data);

        $this->expectException(BatchNotFoundException::class);

        $instance->getShipped('orderRef22');
    }

    /**
     * Generate tickets based on $this->data
     */
    private function generateTickets()
    {
        foreach ($this->data as $operation => $tickets) {
            foreach ($tickets as $ticketId => $orders) {
                $ticket = $this->createMock(BatchResource::class);
                $ticket
                    ->method('getId')
                    ->willReturn($ticketId);

                $this->tickets[] = $ticket;
            }
        }
    }

    /**
     * Generate wrong tickets
     */
    private function generateWrongTickets()
    {
        foreach ($this->data as $operation => $tickets) {
            foreach ($tickets as $ticketId => $orders) {
                $ticket = $this->createMock(BatchResource::class);
                $ticket
                    ->method('getId')
                    ->willReturn($ticketId . '22');

                $this->tickets[] = $ticket;
            }
        }
    }
}
