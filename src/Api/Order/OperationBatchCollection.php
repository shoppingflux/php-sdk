<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Task;
use ShoppingFeed\Sdk\Order;

class OperationBatchCollection extends Task\BatchCollection
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
     * @return Task\BatchResource[]
     */
    public function getShipped($reference = null)
    {
        return $this->findBatchs(
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
     * @return Task\BatchResource[]
     */
    public function getAccepted($reference = null)
    {
        return $this->findBatchs(
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
     * @return Task\BatchResource[]
     */
    public function getRefused($reference = null)
    {
        return $this->findBatchs(
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
     * @return Task\BatchResource[]
     */
    public function getRefunded($reference = null)
    {
        return $this->findBatchs(
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
     * @return Task\BatchResource[]
     */
    public function getCanceled($reference = null)
    {
        return $this->findBatchs(
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
     * @return Task\BatchResource[]
     */
    public function getAcknowledge($reference = null)
    {
        return $this->findBatchs(
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
     * @return Task\BatchResource[]
     */
    public function getUnacknowledge($reference = null)
    {
        return $this->findBatchs(
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
     * @return Task\BatchResource[]
     */
    protected function findBatchs(array $criteria = [])
    {
        $batchs = [];

        if (! isset($criteria['operation']) && ! isset($criteria['reference'])) {
            $batchs = (array) $this->getIterator();
        } elseif (isset($criteria['operation'])) {
            if (! isset($criteria['reference'])) {
                $batchs = $this->getBatchsById(
                    array_keys($this->ticketReferences[$criteria['operation']])
                );
            } else {
                foreach ($this->ticketReferences[$criteria['operation']] as $batchId => $orders) {
                    if (in_array($criteria['reference'], $orders, true)) {
                        $batchs = $this->getBatchsById([$batchId]);
                        break;
                    }
                }
            }
        }

        return $batchs;
    }

    /**
     * Find ticket in collection by its ID
     *
     * @param array $ids
     *
     * @return Task\BatchResource[]
     */
    private function getBatchsById(array $ids)
    {
        $batchs = [];
        foreach ($this->getIterator() as $batch) {
            /** @var Task\BatchResource $batch */
            if (in_array($batch->getId(), $ids, false)) {
                $batchs[] = $batch;
            }
        }

        return $batchs;
    }
}
