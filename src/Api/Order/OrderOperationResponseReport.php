<?php

namespace ShoppingFeed\Sdk\Api\Order;

class OrderOperationResponseReport
{
    private ?int $id;
    private ?string $channelName;

    private ?string $reference;

    private string $state;

    private string $message;

    /**
     * @param array{
     *      id: int|null,
     *      channelName: string|null,
     *      reference: string|null,
     *      state: string,
     *      message: string
     *  } $report
     */
    public function __construct(array $report)
    {
        $this->id = $report['id'];
        $this->channelName = $report['channelName'];
        $this->reference = $report['reference'];
        $this->state = $report['state'];
        $this->message = $report['message'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannelName(): ?string
    {
        return $this->channelName;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
