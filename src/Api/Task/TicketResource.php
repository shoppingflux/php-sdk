<?php
namespace ShoppingFeed\Sdk\Api\Task;

use ShoppingFeed\Sdk\Resource\AbstractResource;

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
}
