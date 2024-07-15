<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api;
use ShoppingFeed\Sdk\Exception;
use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Operation\AbstractBulkOperation;
use ShoppingFeed\Sdk\Operation\OperationInterface;

class OrderOperation extends AbstractBulkOperation implements OperationInterface
{
    /**
     * @var array
     */
    protected $operations = [];

    /**
     * @var Operation $operation
     */
    private $operation;

    public function __construct()
    {
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
    public function accept(string $reference, string $channelName, string $reason = ''): self
    {
        $this->operation->addOperation(
            new Api\Order\Identifier\Reference($reference, $channelName),
            Api\Order\Operation::TYPE_ACCEPT,
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
    public function cancel(string $reference, string $channelName, string $reason = ''): self
    {
        $this->operation->addOperation(
            new Api\Order\Identifier\Reference($reference, $channelName),
            Api\Order\Operation::TYPE_CANCEL,
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
        string $reference,
        string $channelName,
        string $carrier = '',
        string $trackingNumber = '',
        string $trackingLink = '',
        array $items = []
    ): self {
        $this->operation->addOperation(
            new Api\Order\Identifier\Reference($reference, $channelName),
            Api\Order\Operation::TYPE_SHIP,
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
    public function refuse(string $reference, string $channelName): self
    {
        $this->operation->addOperation(
            new Api\Order\Identifier\Reference($reference, $channelName),
            Api\Order\Operation::TYPE_REFUSE
        );

        return $this;
    }

    /**
     * Acknowledge order reception
     *
     * @throws Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function acknowledge(
        string $reference,
        string $channelName,
        string $storeReference = '',
        string $status = 'success',
        string $message = ''
    ): self {
        $acknowledgedAt = date_create()->format('c');

        $this->operation->addOperation(
            new Api\Order\Identifier\Reference($reference, $channelName),
            Api\Order\Operation::TYPE_ACKNOWLEDGE,
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
    public function unacknowledge(string $reference, string $channelName): self
    {
        $this->operation->addOperation(
            new Api\Order\Identifier\Reference($reference, $channelName),
            Api\Order\Operation::TYPE_UNACKNOWLEDGE
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
    public function uploadDocument(string $reference, string $channelName, Document\AbstractDocument $document): self
    {
        $this->operation->addOperation(
            new Api\Order\Identifier\Reference($reference, $channelName),
            Api\Order\Operation::TYPE_UPLOAD_DOCUMENTS,
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
    public function refund(string $reference, string $channelName, bool $shipping = true, array $products = []): self
    {
        $this->operation->addOperation(
            new Api\Order\Identifier\Reference($reference, $channelName),
            Api\Order\Operation::TYPE_REFUND,
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
    public function addOperation(string $reference, string $channelName, string $type, array $data = []): void
    {
        $this->operation->addOperation(new Api\Order\Identifier\Reference($reference, $channelName), $type, $data);
    }
}
