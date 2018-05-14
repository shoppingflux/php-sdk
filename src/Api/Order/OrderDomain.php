<?php
namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Order as ApiOrder;
use ShoppingFeed\Sdk\Order\OrderOperation;
use ShoppingFeed\Sdk\Resource\AbstractDomainResource;

/**
 * @method ApiOrder\OrderResource[] getIterator()
 * @method ApiOrder\OrderResource[] getAll($page = 1, $limit = 100)
 */
class OrderDomain extends AbstractDomainResource
{
    /**
     * @var OrderOperation
     */
    private $orderOperations;

    /**
     * @var string
     */
    protected $resourceClass = ApiOrder\OrderResource::class;

    /**
     * Init new order operation queue
     *
     * @return OrderDomain
     */
    public function newOrderOperation()
    {
        $this->orderOperations = new OrderOperation();

        return $this;
    }

    /**
     * Notify market place of order acceptance
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     * @param string $reason      Optional reason of acceptance
     *
     * @return OrderDomain
     *
     * @throws \Exception
     */
    public function accept($reference, $channelName, $reason = '')
    {
        $this->orderOperations->addOperation(
            $reference,
            $channelName,
            OrderOperation::TYPE_ACCEPT,
            compact('reason')
        );

        return $this;
    }

    /**
     * Notify market place of order cancellation
     *
     * @param string $reference   Order reference
     * @param string $channelName Channel to notify
     * @param string $reason      Optional reason of cancellation
     *
     * @return OrderDomain
     *
     * @throws \Exception
     */
    public function cancel($reference, $channelName, $reason = '')
    {
        $this->orderOperations->addOperation(
            $reference,
            $channelName,
            OrderOperation::TYPE_CANCEL,
            compact('reason')
        );

        return $this;
    }

    /**
     * Notify market place of order shipment sent
     *
     * @param string $reference      Order reference
     * @param string $channelName    Channel to notify
     * @param string $carrier        Optional carrier name
     * @param string $trackingNumber Optional tracking number
     * @param string $trackingLink   Optional tracking link
     *
     * @return OrderDomain
     *
     * @throws \Exception
     */
    public function ship($reference, $channelName, $carrier = '', $trackingNumber = '', $trackingLink = '')
    {
        $this->orderOperations->addOperation(
            $reference,
            $channelName,
            OrderOperation::TYPE_SHIP,
            compact('carrier', 'trackingNumber', 'trackingLink')
        );

        return $this;
    }

    /**
     * Notify market place of order refusal
     *
     * @param string $reference Order reference
     * @param string $channelName    Channel to notify
     * @param array  $refund    Order item reference that will be refunded
     *
     * @return OrderDomain
     *
     * @throws \Exception
     */
    public function refuse($reference, $channelName, $refund = [])
    {
        $this->orderOperations->addOperation(
            $reference,
            $channelName,
            OrderOperation::TYPE_REFUSE,
            compact('refund')
        );

        return $this;
    }

    /**
     * Execute order operations
     *
     * @return mixed|OrderCollection
     */
    public function execute()
    {
        return $this->orderOperations->execute($this->link);
    }
}
