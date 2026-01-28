<?php

namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Hal\HalResource;

/**
 * This class was designed this way to make possible to add the request associate to the batch in future version.
 */
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
