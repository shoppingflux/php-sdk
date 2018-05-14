<?php
namespace ShoppingFeed\Sdk\Order;

use ShoppingFeed\Sdk\Api\Order\OrderCollection;
use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Operation\AbstractBulkOperation;

class OrderOperation extends AbstractBulkOperation
{
    /**
     * Operation types
     */
    const TYPE_ACCEPT = 'accept';
    const TYPE_CANCEL = 'cancel';
    const TYPE_REFUSE = 'refuse';
    const TYPE_SHIP   = 'ship';

    /**
     * @var array
     */
    private $allowedOperationTypes = [
        self::TYPE_ACCEPT,
        self::TYPE_CANCEL,
        self::TYPE_REFUSE,
        self::TYPE_SHIP,
    ];

    /**
     * @var array
     */
    protected $operations = [];

    /**
     * Execute all declared operations
     *
     * @param Hal\HalLink $link
     *
     * @return mixed|OrderCollection
     */
    public function execute(Hal\HalLink $link)
    {
        // Create requests per batch
        $requests = [];
        foreach ($this->allowedOperationTypes as $type) {
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

        // Send requests
        $resources = [];
        $link->batchSend(
            $requests,
            function (Hal\HalResource $resource) use (&$resources) {
                array_push($resources, ...$resource->getResources('order'));
            },
            null,
            [],
            $this->getPoolSize()
        );

        return new OrderCollection($resources);
    }

    /**
     * Add operation to queue
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     * @param string $type        Type of operation
     * @param array  $data        Extra data to pass to operation call
     *
     * @throws \Exception
     */
    public function addOperation($reference, $channelName, $type, $data = [])
    {
        if (! in_array($type, $this->allowedOperationTypes)) {
            throw new \Exception(sprintf(
                'Only %s operations are accepted',
                implode(', ', $this->allowedOperationTypes)
            ));
        }

        if (! isset($this->operations[$type])) {
            $this->operations[$type] = [];
        }

        $this->operations[$type][] = array_merge(compact('reference', 'channelName'), $data);
    }
}
