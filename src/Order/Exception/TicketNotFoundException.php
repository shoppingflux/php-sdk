<?php
namespace ShoppingFeed\Sdk\Order\Exception;

class TicketNotFoundException extends \Exception
{
    /**
     * Create exception for ticket not found for operation
     *
     * @param string $operation
     *
     * @return TicketNotFoundException
     */
    public static function withOperation($operation)
    {
        return new self(sprintf(
            'No ticket found for operation %s',
            $operation
        ));
    }

    /**
     * Create exception for ticket not found for operation and order refence
     *
     * @param string $operation
     * @param string $reference
     *
     * @return TicketNotFoundException
     */
    public static function withOperationAndOrder($operation, $reference)
    {
        return new self(sprintf(
            'No ticket found for operation %s and reference %s',
            $operation,
            $reference
        ));
    }

    /**
     * Create exception for ticket not found by its id
     *
     * @param string $id
     *
     * @return TicketNotFoundException
     */
    public static function withId($id)
    {
        return new self(sprintf(
            'No ticket with id %s found in this collection',
            $id
        ));
    }
}
