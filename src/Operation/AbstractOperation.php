<?php

namespace ShoppingFeed\Sdk\Operation;

use ShoppingFeed\Sdk\Hal;

abstract class AbstractOperation
{
    /**
     *
     * @return mixed
     */
    abstract public function execute(Hal\HalLink $link);
}
