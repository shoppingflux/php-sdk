<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Order;
use ShoppingFeed\Sdk\Api\Task;

class OrderTicketCollection extends Task\TicketCollection
{
    /**
     * Order ticket ID association
     *
     * @var array ['ticket-id' => ['order-ref']]
     */
    private $ticketReferences = [];

    public function __construct(
        array $resources = [],
        array $ticketReferences = []
    )
    {
        parent::__construct($resources);

        $this->ticketReferences = $ticketReferences;
    }

    /**
     * Get ticket for shiped reference
     *
     * @param $reference
     *
     * @return Task\TicketResource[]
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getShipped($reference = null)
    {
        return $this->findTickets(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_SHIP,
            ]
        );
    }

    /**
     * Get ticket for accepted reference
     *
     * @param $reference
     *
     * @return Task\TicketResource[]
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getAccepted($reference = null)
    {
        return $this->findTickets(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_ACCEPT,
            ]
        );
    }

    /**
     * Get ticket for accepted reference
     *
     * @param $reference
     *
     * @return Task\TicketResource[]
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getRefused($reference = null)
    {
        return $this->findTickets(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_REFUSE,
            ]
        );
    }

    /**
     * Get ticket for accepted reference
     *
     * @param $reference
     *
     * @return Task\TicketResource[]
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getCanceled($reference = null)
    {
        return $this->findTickets(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_CANCEL,
            ]
        );
    }

    /**
     * Get ticket for acknowledged reference
     *
     * @param $reference
     *
     * @return Task\TicketResource[]
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getAcknowledge($reference = null)
    {
        return $this->findTickets(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_ACKNOWLEDGE,
            ]
        );
    }

    /**
     * Get ticket for unacknowledged reference
     *
     * @param $reference
     *
     * @return Task\TicketResource[]
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getUnacknowledge($reference = null)
    {
        return $this->findTickets(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_UNACKNOWLEDGE,
            ]
        );
    }

    /**
     * Find tickets for an operation on an order
     *
     * @param array $criteria Criteria to find tickets ['reference' => 'xxx', 'operation" => 'xxx']
     *
     * @return Task\TicketResource[]
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    protected function findTickets(array $criteria = [])
    {
        if (! isset($criteria['operation']) && ! isset($criteria['reference'])) {
            return (array) $this->getIterator();
        }

        if (isset($criteria['operation']) && ! isset($this->ticketReferences[$criteria['operation']])) {
            return [];
        }

        if (isset($criteria['operation']) && ! isset($criteria['reference'])) {
            return $this->getTicketsById(
                array_keys($this->ticketReferences[$criteria['operation']])
            );
        }

        foreach ($this->ticketReferences[$criteria['operation']] as $ticketId => $orders) {
            if (in_array($criteria['reference'], $orders)) {
                return $this->getTicketsById([$ticketId]);
            }
        }

        throw Order\Exception\TicketNotFoundException::forOperationAndOrder(
            $criteria['operation'],
            $criteria['reference']
        );
    }

    /**
     * Find ticket in collection by its ID
     *
     * @param array $ids
     *
     * @return Task\TicketResource[]
     */
    private function getTicketsById(array $ids)
    {
        $tickets = [];
        foreach ($this->getIterator() as $ticket) {
            /** @var Task\TicketResource $ticket */
            if (in_array($ticket->getId(), $ids)) {
                $tickets[] = $ticket;
            }
        }

        return $tickets;
    }
}
