<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Task;
use ShoppingFeed\Sdk\Hal\HalResource;
use ShoppingFeed\Sdk\Exception;

class OrderOperationResult
{
    /**
     * @var Task\TicketDomain[]
     */
    private $batches;

    public function __construct(array $resources = [])
    {
        $this->setBatches($resources);
    }

    /**
     * Get the iterator for all tickets related to the operation
     *
     * @return Task\TicketResource[]|\Traversable
     */
    public function getTickets()
    {
        foreach ($this->batches as $id => $domain) {
            yield from $domain->getByBatch($id);
        }
    }

    /**
     * Wait for all tickets to be processed.
     *
     * @param int $timeout Seconds to wait
     *
     * @return $this                      The current instance
     * @throws Exception\RuntimeException When the timeout is reached
     */
    public function wait($timeout = null)
    {
        foreach ($this->batches as $id => $domain) {
            $domain->getByBatch($id)->wait($timeout);
        }

        return $this;
    }

    /**
     * @var HalResource[] $resources
     */
    private function setBatches(array $resources)
    {
        $this->batches = [];
        foreach ($resources as $resource) {
            $batchId = $resource->getProperty('id');
            $domain  = new Task\TicketDomain($resource->getLink('ticket'));

            $this->batches[$batchId] = $domain;
        }
    }
}
