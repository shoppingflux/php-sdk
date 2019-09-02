<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use ShoppingFeed\Sdk\Api;
use ShoppingFeed\Sdk\Order;

class OperationBatchCollectionMock extends Api\Order\OperationBatchCollection
{
    /**
     * Change accessibility for test
     *
     * @param array $criteria
     *
     * @return Api\Task\TicketCollection[]
     */
    public function findBatches(array $criteria = [])
    {
        return parent::findBatches($criteria);
    }
}
