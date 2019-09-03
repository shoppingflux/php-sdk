<?php
namespace ShoppingFeed\Sdk\Api\Task;

use ShoppingFeed\Sdk\Exception;
use ShoppingFeed\Sdk\Resource\PaginatedResourceIterator;

class TicketIterator extends PaginatedResourceIterator
{
    /**
     * Wait for all related ticket in collection to be processed.
     * The SDK will try a new call every second forever or until the defined timeout is reached.
     *
     * @param int $timeout Recommended to be defined, seconds to wait before fails
     *
     * @return $this The new instance of TicketIterator with up-to-date tickets
     * @throws Exception\RuntimeException When the timeout is reached
     */
    public function wait($timeout = null)
    {
        $until    = null;
        $timeout  = (int) $timeout;
        $instance = $this;

        if ($timeout > 0) {
            $until = time() + (int) $timeout;
        }

        while ($instance->isBeingProcessed()) {
            if (null !== $until && time() >= $until) {
                throw new Exception\RuntimeException(
                    sprintf('Process timed out after %d seconds', $timeout)
                );
            }

            $instance = $this->refresh();
            sleep(1);
        }

        return $instance;
    }

    /**
     * Determine if at least one ticket in paginated collection is still being processed
     *
     * @return bool
     */
    public function isBeingProcessed()
    {
       return (bool) $this->getMeta('processing');
    }
}
