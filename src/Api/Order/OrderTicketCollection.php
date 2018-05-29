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

    /**
     * Associate an order reference to a ticket
     *
     * @param array $ticketReferences
     *
     * @return OrderTicketCollection
     */
    public function setTicketReferences($ticketReferences)
    {
        $this->ticketReferences = (array) $ticketReferences;

        return $this;
    }

    /**
     * Get ticket for shiped reference
     *
     * @param $reference
     *
     * @return Task\TicketResource
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getShippedTicket($reference)
    {
        return $this->findTicketByOrder($reference, Order\OrderOperation::TYPE_SHIP);
    }

    /**
     * Get ticket for accepted reference
     *
     * @param $reference
     *
     * @return Task\TicketResource
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getAcceptedTicket($reference)
    {
        return $this->findTicketByOrder($reference, Order\OrderOperation::TYPE_ACCEPT);
    }

    /**
     * Get ticket for accepted reference
     *
     * @param $reference
     *
     * @return Task\TicketResource
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getRefusedTicket($reference)
    {
        return $this->findTicketByOrder($reference, Order\OrderOperation::TYPE_REFUSE);
    }

    /**
     * Get ticket for accepted reference
     *
     * @param $reference
     *
     * @return Task\TicketResource
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getCanceledTicket($reference)
    {
        return $this->findTicketByOrder($reference, Order\OrderOperation::TYPE_CANCEL);
    }

    /**
     * Get ticket for acknowledged reference
     *
     * @param $reference
     *
     * @return Task\TicketResource
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getAcknowledgeTicket($reference)
    {
        return $this->findTicketByOrder($reference, Order\OrderOperation::TYPE_ACKNOWLEDGE);
    }

    /**
     * Get ticket for unacknowledged reference
     *
     * @param $reference
     *
     * @return Task\TicketResource
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    public function getUnacknowledgeTicket($reference)
    {
        return $this->findTicketByOrder($reference, Order\OrderOperation::TYPE_UNACKNOWLEDGE);
    }

    /**
     * Find a ticket for an operation on an order
     *
     * @param string $reference Order reference
     * @param string $operation Operation name
     *
     * @return Task\TicketResource
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    protected function findTicketByOrder($reference, $operation)
    {
        if (! isset($this->ticketReferences[$operation])) {
            throw Order\Exception\TicketNotFoundException::withOperation($operation);
        }

        foreach ($this->ticketReferences[$operation] as $ticketId => $orders) {
            if (in_array($reference, $orders)) {
                return $this->getTicketById($ticketId);
            }
        }

        throw Order\Exception\TicketNotFoundException::withOperationAndOrder($operation, $reference);
    }

    /**
     * Find ticket in collection by its ID
     *
     * @param string $id
     *
     * @return Task\TicketResource
     *
     * @throws Order\Exception\TicketNotFoundException
     */
    private function getTicketById($id)
    {
        foreach ($this->getIterator() as $ticket) {
            /** @var Task\TicketResource $ticket */
            if ($ticket->getId() === $id) {
                return $ticket;
            }
        }

        throw Order\Exception\TicketNotFoundException::withId($id);
    }
}
