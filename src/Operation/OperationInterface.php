<?php

namespace ShoppingFeed\Sdk\Operation;

use ShoppingFeed\Sdk\Api\Order\OrderOperationResult;
use ShoppingFeed\Sdk\Hal\HalLink;

interface OperationInterface
{
    public function execute(HalLink $link): OrderOperationResult;
}
