<?php

namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Task\TicketDomain;

class OrderOperationResponseBatch
{
    public function __construct(private readonly string $id, private readonly TicketDomain $ticket)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTicket(): TicketDomain
    {
        return $this->ticket;
    }
}
