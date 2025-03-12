<?php

namespace ShoppingFeed\Sdk\Api\Order;

use ArrayAccess;
use ArrayObject;
use Exception;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use ShoppingFeed\Sdk\Api\Order\Document\AbstractDocument;
use ShoppingFeed\Sdk\Api\Order\Identifier\OrderIdentifier;
use ShoppingFeed\Sdk\Exception\InvalidArgumentException;
use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Operation\AbstractBulkOperation;
use ShoppingFeed\Sdk\Operation\OperationInterface;

final class Operation extends AbstractBulkOperation implements OperationInterface
{
    /**
     * Operation types
     */
    private const TYPE_ACCEPT           = 'accept';
    private const TYPE_CANCEL           = 'cancel';
    private const TYPE_REFUSE           = 'refuse';
    private const TYPE_SHIP             = 'ship';
    private const TYPE_REFUND           = 'refund';
    private const TYPE_ACKNOWLEDGE      = 'acknowledge';
    private const TYPE_UNACKNOWLEDGE    = 'unacknowledge';
    private const TYPE_UPLOAD_DOCUMENTS = 'upload-documents';
    private const TYPE_DELIVER          = 'deliver';

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
        return $this->addOperation($identifier, self::TYPE_ACCEPT, compact('reason'));
    }

    /**
     * Notify marketplace of order refusal
     *
     * @throws InvalidArgumentException
     */
    public function refuse(OrderIdentifier $identifier): self
    {
        return $this->addOperation($identifier, self::TYPE_REFUSE);
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
        array $items = [],
        ?ShipReturnInfo $returnInfo = null,
        ?string $warehouseId = null
    ): self
    {
        $data = [
            'carrier'        => $carrier,
            'trackingNumber' => $trackingNumber,
            'trackingLink'   => $trackingLink,
            'items'          => $items,
            'warehouseId'    => $warehouseId,
        ];

        if ($returnInfo) {
            $data['returnInfo'] = $returnInfo->toArray();
        }

        return $this->addOperation($identifier, self::TYPE_SHIP, $data);
    }

    /**
     * Notify marketplace of order cancellation
     *
     * @throws InvalidArgumentException
     */
    public function cancel(OrderIdentifier $identifier, string $reason = ''): self
    {
        return $this->addOperation($identifier, self::TYPE_CANCEL, compact('reason'));
    }

    /**
     * Notify marketplace of order refund
     *
     * @throws InvalidArgumentException
     */
    public function refund(OrderIdentifier $identifier, bool $shipping = true, array $products = []): self
    {
        return $this->addOperation(
            $identifier,
            self::TYPE_REFUND,
            ['refund' => compact('shipping', 'products')]
        );
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

        return $this->addOperation(
            $identifier,
            self::TYPE_ACKNOWLEDGE,
            compact('status', 'storeReference', 'acknowledgedAt', 'message')
        );
    }

    /**
     * Unacknowledge order reception
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function unacknowledge(OrderIdentifier $identifier): self
    {
        return $this->addOperation($identifier, self::TYPE_UNACKNOWLEDGE);
    }

    /**
     * Upload document
     *
     * @throws InvalidArgumentException
     */
    public function uploadDocument(OrderIdentifier $identifier, Document\AbstractDocument $document): self
    {
        return $this->addOperation($identifier, self::TYPE_UPLOAD_DOCUMENTS, ['document' => $document]);
    }

    /**
     * Notify marketplace of order delivery
     *
     * @throws InvalidArgumentException
     */
    public function deliver(OrderIdentifier $identifier): self
    {
        return $this->addOperation($identifier, self::TYPE_DELIVER);
    }

    private function addOperation(OrderIdentifier $identifier, string $type, array $data = []): self
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

        return $this;
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
        // Upload documents require dedicated processing because of file upload specificities
        if (self::TYPE_UPLOAD_DOCUMENTS === $type) {
            $this->populateRequestsForUploadDocuments($link, $requests);
            return;
        }

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

    /**
     * Create requests for upload documents operation. We batch request by 20
     * to not send too many files at once.
     *
     * @param ArrayAccess<int, RequestInterface> $requests
     */
    private function populateRequestsForUploadDocuments(Hal\HalLink $link, ArrayAccess $requests): void
    {
        $type = self::TYPE_UPLOAD_DOCUMENTS;

        foreach (array_chunk($this->getOperations($type), 20) as $batch) {
            $body   = [];
            $orders = [];

            foreach ($batch as $operation) {
                /** @var AbstractDocument $document */
                $document = $operation['document'];
                $resource = fopen($document->getPath(), 'rb');

                if (false === $resource) {
                    throw new RuntimeException(sprintf('Unable to read "%s"', $document->getPath()));
                }

                $body[]   = ['name' => 'files[]', 'contents' => $resource];
                $orders[] = [
                    'id'          => $operation['id'] ?? null,
                    'reference'   => $operation['reference'] ?? null,
                    'channelName' => $operation['channelName'] ?? null,
                    'documents'   => [['type' => $document->getType()]],
                ];
            }

            $body[] = ['name' => 'body', 'contents' => json_encode(['order' => $orders])];

            $requests[] = $link->createRequest(
                'POST',
                ['operation' => $type],
                $body,
                ['Content-Type' => 'multipart/form-data']
            );
        }
    }
}
