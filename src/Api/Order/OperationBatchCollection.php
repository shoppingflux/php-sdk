<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Task;
use ShoppingFeed\Sdk\Order;

class OperationBatchCollection extends Task\TicketCollection
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
     * @return Task\TicketCollection[]
     */
    public function getShipped($reference = null)
    {
        return $this->findBatches(
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
     * @return Task\TicketCollection[]
     */
    public function getAccepted($reference = null)
    {
        return $this->findBatches(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_ACCEPT,
            ]
        );
    }

    /**
     * Get ticket for refused reference
     *
     * @param $reference
     *
     * @return Task\TicketCollection[]
     */
    public function getRefused($reference = null)
    {
        return $this->findBatches(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_REFUSE,
            ]
        );
    }

    /**
     * Get ticket for refunded reference
     *
     * @param $reference
     *
     * @return Task\TicketCollection[]
     */
    public function getRefunded($reference = null)
    {
        return $this->findBatches(
            [
                'reference' => $reference,
                'operation' => OrderOperation::TYPE_REFUND,
            ]
        );
    }

    /**
     * Get ticket for accepted reference
     *
     * @param $reference
     *
     * @return Task\TicketCollection[]
     */
    public function getCanceled($reference = null)
    {
        return $this->findBatches(
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
     * @return Task\TicketCollection[]
     */
    public function getAcknowledge($reference = null)
    {
        return $this->findBatches(
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
     * @return Task\TicketCollection[]
     */
    public function getUnacknowledge($reference = null)
    {
        return $this->findBatches(
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
     * @return Task\TicketCollection[]
     */
    protected function findBatches(array $criteria = [])
    {
        $batches = [];
        if (! isset($criteria['operation']) && ! isset($criteria['reference'])) {
            $batches = (array) $this->getIterator();

        } elseif (isset($criteria['operation'])) {
            if (! isset($criteria['reference'])) {
                $batches = $this->getBatchsById(
                    array_keys($this->ticketReferences[$criteria['operation']])
                );

            } elseif (isset($this->ticketReferences[$criteria['operation']])) {
                foreach ($this->ticketReferences[$criteria['operation']] as $batchId => $orders) {
                    if (in_array($criteria['reference'], $orders, true)) {
                        $batches = $this->getBatchsById([$batchId]);
                        break;
                    }
                }
            }
        }

        return $batches;
    }

    /**
     * Find ticket in collection by its ID
     *
     * @param array $ids
     *
     * @return Task\TicketCollection[]
     */
    private function getBatchsById(array $ids)
    {
        $batches = [];
        foreach ($this->getIterator() as $batch) {
            /** @var Task\TicketResource $batch */
            if (in_array($batch->getId(), $ids, false)
                && $ticketCollection = $batch->fetchBatchTickets()
            ) {
                $batches[] = $ticketCollection;
            }
        }

        return $batches;
    }
}
