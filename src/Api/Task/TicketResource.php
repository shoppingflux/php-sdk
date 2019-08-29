<?php
namespace ShoppingFeed\Sdk\Api\Task;

use ShoppingFeed\Sdk\Resource\AbstractResource;
use ShoppingFeed\Sdk\Resource\PaginatedResourceCollection;

class TicketResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getId()
    {
        return $this->getProperty('id');
    }

    /**
     * @return string
     */
    public function getBatchId()
    {
        return $this->getProperty('batchId');
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getProperty('state');
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getScheduledAt()
    {
        return $this->getPropertyDatetime('scheduledAt');
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getStartedAt()
    {
        return $this->getPropertyDatetime('startedAt');
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getFinishedAt()
    {
        return $this->getPropertyDatetime('finishedAt');
    }

    /**
     * In case a ticket is a batch we need to be able to load its tickets
     *
     * @return null|PaginatedResourceCollection
     */
    public function loadBatchTickets()
    {
        $link = $this->resource->getLink('ticket');
        if (null !== $link) {
            return new PaginatedResourceCollection(
                $link->get(),
                TicketResource::class
            );
        }

        return null;
    }
}
