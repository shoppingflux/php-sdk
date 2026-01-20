<?php

namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Api\Task\TicketDomain;
use ShoppingFeed\Sdk\Hal\HalResource;

class OrderOperationResponse
{
    /** @var array{batch: OrderOperationResponseBatch, report: OrderOperationResponseReport[]} */
    private array $response;

    public function __construct(HalResource $resource)
    {
        $batchId = $resource->getProperty('id');

        $this->response = [
            'batch' => new OrderOperationResponseBatch($batchId, new TicketDomain($resource->getLink('ticket'))),
            'report' => $this->createReport($resource->getProperty('report')),
        ];
    }

    /**
     * @param array{int, array{
     *      id: int|null,
     *      channelName: string|null,
     *      reference: string|null,
     *      state: string,
     *      message: string
     *  }} $reports
     * @return OrderOperationResponseReport[]
     */
    private function createReport(array $reports): array
    {
        $reportItems = [];

        foreach ($reports as $report) {
            if (! is_array($report)) {
                continue;
            }

            $reportItems[] = new OrderOperationResponseReport($report);
        }

        return $reportItems;
    }

    private function getBatch(): OrderOperationResponseBatch
    {
        return $this->response['batch'];
    }

    /**
     * @return OrderOperationResponseReport[]
     */
    public function getReport(): array
    {
        return $this->response['report'];
    }
}
