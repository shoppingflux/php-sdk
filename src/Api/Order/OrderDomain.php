<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Order as ApiOrder;
use ShoppingFeed\Sdk\Resource\AbstractDomainResource;

/**
 * @method ApiOrder\OrderResource[] getIterator()
 * @method ApiOrder\OrderResource[] getAll($criteria = [])
 * @method ApiOrder\OrderResource[] getPage(array $criteria = [])
 * @method ApiOrder\OrderResource[] getPages(array $criteria = [])
 */
class OrderDomain extends AbstractDomainResource
{
    /**
     * @var string
     */
    protected $resourceClass = ApiOrder\OrderResource::class;

    /**
     * @param OrderOperation $operation
     *
     * @return OrderTicketCollection
     */
    public function execute(OrderOperation $operation)
    {
        return $operation->execute($this->link);
    }
}
