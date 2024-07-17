<?php

namespace ShoppingFeed\Sdk\Api\Order;

use ArrayAccess;
use ArrayObject;
use Exception;
use ShoppingFeed\Sdk\Api\Order\Identifier\OrderIdentifier;
use ShoppingFeed\Sdk\Exception\InvalidArgumentException;
use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Operation\AbstractBulkOperation;
use ShoppingFeed\Sdk\Operation\OperationInterface;

class Operation extends AbstractBulkOperation implements OperationInterface
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
     * @var string[]
     */
    private $allowedOperationTypes = [
        self::TYPE_ACCEPT,
        self::TYPE_CANCEL,
        self::TYPE_REFUSE,
        self::TYPE_SHIP,
        self::TYPE_REFUND,
        self::TYPE_ACKNOWLEDGE,
        self::TYPE_UNACKNOWLEDGE,
        self::TYPE_UPLOAD_DOCUMENTS,
        self::TYPE_DELIVER,
    ];

    /**
     * Notify marketplace of order acceptance
     *
     * @throws InvalidArgumentException
     */
    public function accept(OrderIdentifier $identifier, string $reason = ''): self
    {
        $this->addOperation($identifier, self::TYPE_ACCEPT, compact('reason'));

        return $this;
    }

    /**
     * Notify marketplace of order refusal
     *
     * @throws InvalidArgumentException
     */
    public function refuse(OrderIdentifier $identifier): self
    {
        $this->addOperation($identifier, self::TYPE_REFUSE);

        return $this;
    }

    /**
     * Notify marketplace of order shipment sent
     *
     * @throws InvalidArgumentException
     */
    public function ship(
        OrderIdentifier $identifier,
        string $carrier = '',
        string $trackingNumber = '',
        string $trackingLink = '',
        array $items = []
    ): self
    {
        $this->addOperation(
            $identifier,
            self::TYPE_SHIP,
            compact('carrier', 'trackingNumber', 'trackingLink', 'items')
        );

        return $this;
    }

    /**
     * Notify marketplace of order cancellation
     *
     * @throws InvalidArgumentException
     */
    public function cancel(OrderIdentifier $identifier, string $reason = ''): self
    {
        $this->addOperation($identifier, self::TYPE_CANCEL, compact('reason'));

        return $this;
    }

    /**
     * Notify marketplace of order refund
     *
     * @throws InvalidArgumentException
     */
    public function refund(OrderIdentifier $identifier, bool $shipping = true, array $products = []): self
    {
        $this->addOperation(
            $identifier,
            self::TYPE_REFUND,
            ['refund' => compact('shipping', 'products')]
        );

        return $this;
    }

    /**
     * Acknowledge order reception
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function acknowledge(
        OrderIdentifier $identifier,
        string $storeReference = '',
        string $status = 'success',
        string $message = ''
    ): self
    {
        $acknowledgedAt = date_create()->format('c');

        $this->addOperation(
            $identifier,
            self::TYPE_ACKNOWLEDGE,
            compact('status', 'storeReference', 'acknowledgedAt', 'message')
        );

        return $this;
    }

    /**
     * Unacknowledge order reception
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function unacknowledge(OrderIdentifier $identifier): self
    {
        $this->addOperation($identifier, self::TYPE_UNACKNOWLEDGE);

        return $this;
    }

    /**
     * Upload document
     *
     * @throws InvalidArgumentException
     */
    public function uploadDocument(OrderIdentifier $identifier, Document\AbstractDocument $document): self
    {
        $this->addOperation($identifier, self::TYPE_UPLOAD_DOCUMENTS, ['document' => $document]);

        return $this;
    }

    /**
     * Notify marketplace of order delivery
     *
     * @throws InvalidArgumentException
     */
    public function deliver(OrderIdentifier $identifier): self
    {
        $this->addOperation($identifier, self::TYPE_DELIVER);

        return $this;
    }

    public function addOperation(OrderIdentifier $identifier, string $type, array $data = []): void
    {
        if (! in_array($type, $this->allowedOperationTypes)) {
            throw new InvalidArgumentException(sprintf(
                'Only %s operations are accepted',
                implode(', ', $this->allowedOperationTypes)
            ));
        }

        if (! isset($this->operations[$type])) {
            $this->operations[$type] = [];
        }

        $this->operations[$type][] = array_merge($identifier->toArray(), $data);
    }

    public function execute(Hal\HalLink $link): OrderOperationResult
    {
        $requests  = new ArrayObject();
        $resources = new ArrayObject();

        foreach ($this->allowedOperationTypes as $type) {
            $this->populateRequests($type, $link, $requests);
        }

        $link->batchSend(
            $requests->getArrayCopy(),
            function (Hal\HalResource $batch) use ($resources) {
                $resources->append($batch);
            },
            null,
            [],
            $this->getPoolSize()
        );

        return new OrderOperationResult(
            $resources->getArrayCopy()
        );
    }

    private function populateRequests(string $type, Hal\HalLink $link, ArrayAccess $requests): void
    {
        $this->eachBatch(
            function (array $chunk) use ($type, $link, &$requests) {
                $requests[] = $link->createRequest(
                    'POST',
                    ['operation' => $type],
                    ['order' => $chunk]
                );
            },
            $type
        );
    }
}
