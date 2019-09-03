<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Api\Order\OrderOperationResult;
use ShoppingFeed\Sdk\Api\Order\OrderOperation;
use ShoppingFeed\Sdk\Api\Task\TicketCollection;
use ShoppingFeed\Sdk\Api\Task\TicketResource;

class OperationBatchCollectionTest extends TestCase
{
    /**
     * @var array
     */
    private $batchesData = [
        OrderOperation::TYPE_ACCEPT      => [
            'batchId123' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'batchId456' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_CANCEL      => [
            'batchId789' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'batchId321' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_SHIP        => [
            'batchId654' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'batchId987' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_REFUSE      => [
            'batchId159' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'batchId753' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_ACKNOWLEDGE => [
            'batchId147' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'batchId258' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        OrderOperation::TYPE_REFUND      => [
            'batchId239' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'batchId446' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
    ];

    /**
     * @var array
     */
    private $ticketsData = [
        'batchId123' => [
            'ticketId123' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId456' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        'batchId456' => [
            'ticketId789' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId321' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        'batchId789' => [
            'ticketId654' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId987' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        'batchId654' => [
            'ticketId159' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId753' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        'batchId987' => [
            'ticketId147' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId258' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
        'batchId147' => [
            'ticketId239' => ['orderRef1', 'orderRef2', 'orderRef3'],
            'ticketId446' => ['orderRef4', 'orderRef5', 'orderRef6'],
        ],
    ];

    public function testFindBatches()
    {
        $batches      = $this->generateTicketBatches();
        $instance     = new OrderOperationResultMock($batches, $this->batchesData);
        $batchesFound = $instance->findBatches(['reference' => 'orderRef5', 'operation' => OrderOperation::TYPE_SHIP]);

        $this->assertInstanceOf(TicketCollection::class, $batchesFound[0]);
        $this->assertCount(2, $batchesFound[0]);
    }

    public function testFindTicketWithNoOperationNoRef()
    {
        $batches  = $this->generateTicketBatches();
        $instance = new OrderOperationResultMock($batches, $this->batchesData);
        $tickets  = $instance->findBatches();

        $this->assertCount(count($batches), $tickets);
    }

    public function testFindTicketForOperation()
    {
        $batches  = $this->generateTicketBatches();
        $instance = new OrderOperationResultMock($batches, $this->batchesData);
        $tickets  = $instance->findBatches(['operation' => OrderOperation::TYPE_SHIP]);

        $this->assertCount(count($this->batchesData[OrderOperation::TYPE_SHIP]), $tickets);
    }

    public function testGetShippedTicket()
    {
        /** @var OrderOperationResult|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderOperationResult::class)
            ->setMethods(['findBatches'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatches')
            ->with(['reference' => 'orderRef5', 'operation' => OrderOperation::TYPE_SHIP])
            ->willReturn(
                $this->createMock(TicketCollection::class)
            );

        $instance->getShipped('orderRef5');
    }

    public function testGetCanceledTicket()
    {
        /** @var OrderOperationResult|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderOperationResult::class)
            ->setMethods(['findBatches'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatches')
            ->with(['reference' => 'orderRef4', 'operation' => OrderOperation::TYPE_CANCEL])
            ->willReturn(
                $this->createMock(TicketCollection::class)
            );

        $instance->getCanceled('orderRef4');
    }

    public function testGetRefusedTicket()
    {
        /** @var OrderOperationResult|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderOperationResult::class)
            ->setMethods(['findBatches'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatches')
            ->with(['reference' => 'orderRef1', 'operation' => OrderOperation::TYPE_REFUSE])
            ->willReturn(
                $this->createMock(TicketCollection::class)
            );

        $instance->getRefused('orderRef1');
    }

    public function testGetRefundedTicket()
    {
        /** @var OrderOperationResult|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderOperationResult::class)
            ->setMethods(['findBatches'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatches')
            ->with(['reference' => 'orderRef1', 'operation' => OrderOperation::TYPE_REFUND])
            ->willReturn(
                $this->createMock(TicketCollection::class)
            );

        $instance->getRefunded('orderRef1');
    }

    public function testGetAcceptedTicket()
    {
        /** @var OrderOperationResult|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderOperationResult::class)
            ->setMethods(['findBatches'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatches')
            ->with(['reference' => 'orderRef3', 'operation' => OrderOperation::TYPE_ACCEPT])
            ->willReturn(
                $this->createMock(TicketCollection::class)
            );

        $instance->getAccepted('orderRef3');
    }

    public function testGetAcknowledgeTicket()
    {
        /** @var OrderOperationResult|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderOperationResult::class)
            ->setMethods(['findBatches'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatches')
            ->with(['reference' => 'orderRef6', 'operation' => OrderOperation::TYPE_ACKNOWLEDGE])
            ->willReturn(
                $this->createMock(TicketCollection::class)
            );

        $instance->getAcknowledge('orderRef6');
    }

    public function testGetUnacknowledgeTicket()
    {
        /** @var OrderOperationResult|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(OrderOperationResult::class)
            ->setMethods(['findBatches'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('findBatches')
            ->with(['reference' => 'orderRef7', 'operation' => OrderOperation::TYPE_UNACKNOWLEDGE])
            ->willReturn(
                $this->createMock(TicketCollection::class)
            );

        $instance->getUnacknowledge('orderRef7');
    }

    public function testFindTicketWrongOperation()
    {
        $tickets  = $this->generateTicketCollections();
        $instance = new OrderOperationResult($tickets);

        $this->assertEquals([], $instance->getShipped('orderRef5'));
    }

    public function testFindTicketWrongReference()
    {
        $tickets  = $this->generateWrongBatch();
        $instance = new OrderOperationResult($tickets, $this->batchesData);

        $this->assertEquals([], $instance->getShipped('orderRef5'));
    }

    /**
     * Generate tickets based on $this->data
     *
     * @param null|string $filterBatchId
     * @param null|[]     $ticketsId
     *
     * @return TicketCollection|TicketCollection[]
     */
    private function generateTicketCollections($filterBatchId = null, $ticketsId = null)
    {
        $ticketCollections = [];

        foreach ($this->ticketsData as $batchId => $tickets) {
            if (! $batchId || $batchId === $filterBatchId) {
                foreach ($tickets as $ticketId => $orders) {
                    if (! $ticketsId || in_array($ticketId, $ticketsId, true)) {
                        $ticket = $this->createMock(TicketResource::class);
                        $ticket
                            ->method('getId')
                            ->willReturn($ticketId);
                        $ticket
                            ->method('fetchBatchTickets')
                            ->willReturn(null);

                        $tickets[] = $ticket;
                    }
                }

                if ($filterBatchId) {
                    return new TicketCollection($tickets);
                }

                $ticketCollections[] = new TicketCollection($tickets);
            }
        }

        return $ticketCollections;
    }

    /**
     * Generate tickets based on $this->data
     */
    private function generateTicketBatches()
    {
        $batches = [];

        foreach ($this->batchesData as $operation => $batchs) {
            foreach ($batchs as $batchId => $tickets) {
                $batch   = $this->createMock(TicketResource::class);
                $tickets = $this->generateTicketCollections($batchId, $tickets);

                $batch
                    ->method('getId')
                    ->willReturn($batchId);
                $batch
                    ->method('fetchBatchTickets')
                    ->willReturn($tickets);

                $batches[] = $batch;
            }
        }

        return $batches;
    }

    /**
     * Generate wrong batch ticket
     */
    private function generateWrongBatch()
    {
        $batches = [];
        foreach ($this->batchesData as $operation => $batchs) {
            foreach ($batchs as $batchId => $tickets) {
                $batch = $this->createMock(TicketResource::class);
                $batch
                    ->method('getId')
                    ->willReturn($batchId . '22');
                $batch
                    ->method('fetchBatchTickets')
                    ->willReturn([]);

                $batches[] = $batch;
            }
        }

        return $batches;
    }
}
