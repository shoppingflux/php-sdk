<?php

namespace ShoppingFeed\Sdk\Api\Order\Document;

class Invoice extends AbstractDocument
{
    public function __construct(string $path)
    {
        parent::__construct($path, 'invoice');
    }
}
