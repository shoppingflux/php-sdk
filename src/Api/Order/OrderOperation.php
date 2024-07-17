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
     * @throws Exception\InvalidArgumentException
     */
    public function accept($reference, $channelName, $reason = ''): self
    {
        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName),
            Operation::TYPE_ACCEPT,
            compact('reason')
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
     * @throws Exception\InvalidArgumentException
     */
    public function cancel($reference, $channelName, $reason = ''): self
    {
        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName),
            Operation::TYPE_CANCEL,
            compact('reason')
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
     * @throws Exception\InvalidArgumentException
     */
    public function ship(
        $reference,
        $channelName,
        $carrier = '',
        $trackingNumber = '',
        $trackingLink = '',
        $items = []
    ): self
    {
        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName),
            Operation::TYPE_SHIP,
            compact('carrier', 'trackingNumber', 'trackingLink', 'items')
        );

        return $this;
    }

    /**
     * Notify marketplace of order refusal
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     *
     * @throws Exception\InvalidArgumentException
     */
    public function refuse($reference, $channelName): self
    {
        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName),
            Operation::TYPE_REFUSE
        );

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
     * @throws Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function acknowledge(
        $reference,
        $channelName,
        $storeReference = '',
        $status = 'success',
        $message = ''
    ): self
    {
        $acknowledgedAt = date_create()->format('c');

        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName),
            Operation::TYPE_ACKNOWLEDGE,
            compact('status', 'storeReference', 'acknowledgedAt', 'message')
        );

        return $this;
    }

    /**
     * Unacknowledge order reception
     *
     * @param string $reference   The channel's order reference
     * @param string $channelName The channel's name
     *
     * @throws Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function unacknowledge($reference, $channelName): self
    {
        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName),
            Operation::TYPE_UNACKNOWLEDGE
        );

        return $this;
    }

    /**
     * @param string                    $reference The channel's order reference
     * @param string                    $channelName The channel's name
     * @param Document\AbstractDocument $document
     *
     * @throws Exception\InvalidArgumentException
     */
    public function uploadDocument($reference, $channelName, Document\AbstractDocument $document): self
    {
        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName),
            Operation::TYPE_UPLOAD_DOCUMENTS,
            ['document' => $document]
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
     * @throws Exception\InvalidArgumentException
     */
    public function refund($reference, $channelName, $shipping = true, $products = []): self
    {
        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName),
            Operation::TYPE_REFUND,
            ['refund' => compact('shipping', 'products')]
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

    /**
     * Add operation to queue
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     * @param string $type        Type of operation
     * @param array  $data        Extra data to pass to operation call
     *
     * @throws Exception\InvalidArgumentException
     */
    public function addOperation($reference, $channelName, $type, $data = []): void
    {
        $this->operation->addOperation(
            $this->createReference((string) $reference, (string) $channelName), (string) $type, (array) $data
        );
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
