<?php
namespace ShoppingFeed\Sdk\Order\Exception;

class TicketNotFoundException extends \Exception
{
    /**
     * Create exception for ticket not found for operation and order refence
     *
     * @param string $operation
     * @param string $reference
     *
     * @return TicketNotFoundException
     */
    public static function forOperationAndOrder($operation, $reference)
    {
        return new self(sprintf(
            'No ticket found for operation %s and reference %s',
            $operation,
            $reference
        ));
    }
}
