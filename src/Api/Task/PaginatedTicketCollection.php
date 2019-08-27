<?php
namespace ShoppingFeed\Sdk\Api\Task;

use ShoppingFeed\Sdk\Resource\PaginatedResourceCollection;

class PaginatedTicketCollection extends PaginatedResourceCollection
{
    /**
     * If one ticket of the collection is still being processed will return true.
     *
     * @return bool
     */
    public function isBeingProcessed()
    {
        $metadata = $this->getMetaData();

        return isset($metadata['processing']) ? $metadata['processing'] : true;
    }
}
