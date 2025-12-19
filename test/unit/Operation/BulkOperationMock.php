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

    public function execute(Hal\HalLink $link)
    {
        return 'Called';
    }
}
