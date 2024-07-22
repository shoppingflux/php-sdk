<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api;
use ShoppingFeed\Sdk\Exception;
use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Operation\AbstractBulkOperation;
use ShoppingFeed\Sdk\Operation\OperationInterface;

/**
 * @deprecated Use ShoppingFeed\Sdk\Api\Order\Operation instead
 */
class OrderOperation extends AbstractBulkOperation implements OperationInterface
{
    /**
     * Operation types
     */
    public const TYPE_ACCEPT           = 'accept';
    public const TYPE_CANCEL           = 'cancel';
    public const TYPE_REFUSE           = 'refuse';
    public const TYPE_SHIP             = 'ship';
    public const TYPE_REFUND           = 'refund';
    public const TYPE_ACKNOWLEDGE      = 'acknowledge';
    public const TYPE_UNACKNOWLEDGE    = 'unacknowledge';
    public const TYPE_UPLOAD_DOCUMENTS = 'upload-documents';
    public const TYPE_DELIVER          = 'deliver';

    /**
     * @var Operation $operation
     */
    private $operation;

    /**
     * @deprecated Use Operation instead
     */
    public function __construct()
    {
        trigger_deprecation(
            'shopping-feed/sdk',
            '0.9.0',
            'The OrderOperation::__construct() method is deprecated and will '
            . 'be removed in 1.0. Use Operation::__construct() instead'
        );

        $this->operation = new Operation();
    }

    /**
     * Notify marketplace of order acceptance
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     * @param string $reason      Optional reason of acceptance
     *
     * @return OrderOperation
     *
     * @throws Exception\InvalidArgumentException
     */
    public function accept($reference, $channelName, $reason = '')
    {
        $this->operation->accept(
            $this->createReference((string) $reference, (string) $channelName),
            (string) $reason
        );

        return $this;
    }

    /**
     * Notify marketplace of order cancellation
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     * @param string $reason      Optional reason of cancellation
     *
     * @return OrderOperation
     *
     * @throws Exception\InvalidArgumentException
     */
    public function cancel($reference, $channelName, $reason = '')
    {
        $this->operation->cancel(
            $this->createReference((string) $reference, (string) $channelName),
            (string) $reason
        );

        return $this;
    }

    /**
     * Notify marketplace of order shipment sent
     *
     * @param string $reference                           Order reference
     * @param string $channelName                         Channel to notify
     * @param string $carrier                             Optional carrier name
     * @param string $trackingNumber                      Optional tracking number
     * @param string $trackingLink                        Optional tracking link
     * @param array<array{id: int, quantity: int}> $items Optional items
     *
     * @return OrderOperation
     *
     * @throws Exception\InvalidArgumentException
     */
    public function ship(
        $reference,
        $channelName,
        $carrier = '',
        $trackingNumber = '',
        $trackingLink = '',
        $items = []
    )
    {
        $this->operation->ship(
            $this->createReference((string) $reference, (string) $channelName),
            (string) $carrier,
            (string) $trackingNumber,
            (string) $trackingLink,
            (array) $items
        );

        return $this;
    }

    /**
     * Notify marketplace of order refusal
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     *
     * @return OrderOperation
     *
     * @throws Exception\InvalidArgumentException
     */
    public function refuse($reference, $channelName)
    {
        $this->operation->refuse($this->createReference((string) $reference, (string) $channelName));

        return $this;
    }

    /**
     * Acknowledge order reception
     *
     * @param string $reference
     * @param string $channelName
     * @param string $storeReference
     * @param string $status
     * @param string $message
     *
     * @return OrderOperation
     *
     * @throws Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function acknowledge($reference, $channelName, $storeReference = '', $status = 'success', $message = '')
    {
        $this->operation->acknowledge(
            $this->createReference((string) $reference, (string) $channelName),
            (string) $storeReference,
            (string) $status,
            (string) $message
        );

        return $this;
    }

    /**
     * Unacknowledge order reception
     *
     * @param string $reference   The channel's order reference
     * @param string $channelName The channel's name
     *
     * @return OrderOperation
     *
     * @throws Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function unacknowledge($reference, $channelName)
    {
        $this->operation->unacknowledge($this->createReference((string) $reference, (string) $channelName));

        return $this;
    }

    /**
     * @param string                    $reference The channel's order reference
     * @param string                    $channelName The channel's name
     * @param Document\AbstractDocument $document
     *
     * @return OrderOperation
     *
     * @throws Exception\InvalidArgumentException
     */
    public function uploadDocument($reference, $channelName, Document\AbstractDocument $document): self
    {
        $this->operation->uploadDocument(
            $this->createReference((string) $reference, (string) $channelName),
            $document
        );

        return $this;
    }

    /**
     * Notify marketplace of order refund
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     * @param bool   $shipping    True to refund shipping costs
     * @param array  $products    Order item reference and quantities that will be refunded
     *
     * @return OrderOperation
     *
     * @throws Exception\InvalidArgumentException
     */
    public function refund($reference, $channelName, $shipping = true, $products = [])
    {
        $this->operation->refund(
            $this->createReference((string) $reference, (string) $channelName),
            (bool) $shipping,
            (array) $products
        );

        return $this;
    }

    /**
     * Execute all declared operations
     */
    public function execute(Hal\HalLink $link): OrderOperationResult
    {
        return $this->operation->execute($link);
    }

    private function createReference(string $reference, string $channelName): Api\Order\Identifier\OrderIdentifier
    {
        return new class implements Api\Order\Identifier\OrderIdentifier {
            /**
             * @var string
             */
            private $reference;

            /**
             * @var string
             */
            private $channelName;

            public function __construct(string $reference, string $channelName)
            {
                $this->reference   = $reference;
                $this->channelName = $channelName;
            }

            /**
             * @return array{reference: string, channel_name: string}
             */
            public function toArray(): array
            {
                return [
                    'reference'    => $this->reference,
                    'channel_name' => $this->channelName,
                ];
            }
        };
    }
}
