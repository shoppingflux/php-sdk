<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use ShoppingFeed\Sdk\Api;
use ShoppingFeed\Sdk\Order;

class OrderOperationResultMock extends Api\Order\OrderOperationResult
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
