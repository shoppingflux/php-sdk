<?php
namespace ShoppingFeed\Sdk\Test\Operation;

use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Operation\AbstractBulkOperation;

class BulkOperationMock extends AbstractBulkOperation
{
    /**
     * @var array
     */
    private $operation;

    public function __construct($operations = [])
    {
        $this->operations = $operations;
    }

    public function eachBatch(callable $callback, $groupedBy = '')
    {
        parent::eachBatch($callback, $groupedBy);
    }

    public function execute(Hal\HalLink $link)
    {
        return 'Called';
    }
}
