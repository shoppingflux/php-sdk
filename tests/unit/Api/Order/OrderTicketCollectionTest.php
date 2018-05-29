<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\OrderTicketCollection;
use ShoppingFeed\Sdk\Api\Task\TicketResource;
use ShoppingFeed\Sdk\Order\Exception\TicketNotFoundException;
use ShoppingFeed\Sdk\Order\OrderOperation;

class OrderTicketCollectionTest extends TestCase
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
        OrderOperation::TYPE_ACKNOWLEDGE => [
            'ticketId369' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId486' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
    ];


    public function testFindTicket()
    {
        $this->generateTickets();
        $instance = new OrderTicketCollection($this->tickets);
        $ticket   = $instance
            ->setTicketReferences($this->data)
            ->getShippedTicket('orderRef5');

        $this->assertInstanceOf(TicketResource::class, $ticket);
        $this->assertEquals('ticketId987', $ticket->getId());
    }

    public function testGetShippedTicket()
    {
        /** @var OrderTicketCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderTicketCollection::class)
            ->setMethods(['findTicketByOrder'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findTicketByOrder')
            ->with('orderRef5', OrderOperation::TYPE_SHIP)
            ->willReturn(
                $this->createMock(TicketResource::class)
            );

        $instance->getShippedTicket('orderRef5');
    }

    public function testGetCanceledTicket()
    {
        /** @var OrderTicketCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderTicketCollection::class)
            ->setMethods(['findTicketByOrder'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findTicketByOrder')
            ->with('orderRef4', OrderOperation::TYPE_CANCEL)
            ->willReturn(
                $this->createMock(TicketResource::class)
            );

        $instance->getCanceledTicket('orderRef4');
    }

    public function testGetRefusedTicket()
    {
        /** @var OrderTicketCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderTicketCollection::class)
            ->setMethods(['findTicketByOrder'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findTicketByOrder')
            ->with('orderRef1', OrderOperation::TYPE_REFUSE)
            ->willReturn(
                $this->createMock(TicketResource::class)
            );

        $instance->getRefusedTicket('orderRef1');
    }

    public function testGetAcceptedTicket()
    {
        /** @var OrderTicketCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderTicketCollection::class)
            ->setMethods(['findTicketByOrder'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findTicketByOrder')
            ->with('orderRef3', OrderOperation::TYPE_ACCEPT)
            ->willReturn(
                $this->createMock(TicketResource::class)
            );

        $instance->getAcceptedTicket('orderRef3');
    }

    public function testGetAcknowledgeTicket()
    {
        /** @var OrderTicketCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderTicketCollection::class)
            ->setMethods(['findTicketByOrder'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findTicketByOrder')
            ->with('orderRef6', OrderOperation::TYPE_ACKNOWLEDGE)
            ->willReturn(
                $this->createMock(TicketResource::class)
            );

        $instance->getAcknowledgeTicket('orderRef6');
    }

    public function testGetUnacknowledgeTicket()
    {
        /** @var OrderTicketCollection|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderTicketCollection::class)
            ->setMethods(['findTicketByOrder'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findTicketByOrder')
            ->with('orderRef7', OrderOperation::TYPE_UNACKNOWLEDGE)
            ->willReturn(
                $this->createMock(TicketResource::class)
            );

        $instance->getUnacknowledgeTicket('orderRef7');
    }

    public function testFindTicketWrongOperation()
    {
        $this->generateTickets();
        $instance = new OrderTicketCollection($this->tickets);

        $this->expectException(TicketNotFoundException::class);

        $instance->getShippedTicket('orderRef5');
    }

    public function testFindTicketWrongReference()
    {
        $this->generateWrongTickets();
        $instance = new OrderTicketCollection($this->tickets);

        $this->expectException(TicketNotFoundException::class);

        $instance
            ->setTicketReferences($this->data)
            ->getShippedTicket('orderRef22');
    }

    public function testTicketNotFound()
    {
        $instance = new OrderTicketCollection($this->tickets);

        $this->expectException(TicketNotFoundException::class);

        $instance
            ->setTicketReferences($this->data)
            ->getShippedTicket('orderRef5');
    }

    private function generateTickets()
    {
        foreach ($this->data as $operation => $tickets) {
            foreach ($tickets as $ticketId => $orders) {
                $ticket = $this->createMock(TicketResource::class);
                $ticket
                    ->method('getId')
                    ->willReturn($ticketId);

                $this->tickets[] = $ticket;
            }
        }
    }

    private function generateWrongTickets()
    {
        foreach ($this->data as $operation => $tickets) {
            foreach ($tickets as $ticketId => $orders) {
                $ticket = $this->createMock(TicketResource::class);
                $ticket
                    ->method('getId')
                    ->willReturn($ticketId . '22');

                $this->tickets[] = $ticket;
            }
        }
    }
}
