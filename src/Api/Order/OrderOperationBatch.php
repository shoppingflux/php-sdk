<?php

namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Hal\HalResource;

class OrderOperationBatch
{
    private OrderOperationResponse $response;

    public function __construct(HalResource $resource)
    {
        $this->response = new OrderOperationResponse($resource);
    }

    public function getResponse(): OrderOperationResponse
    {
        return $this->response;
    }
}
