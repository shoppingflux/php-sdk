<?php

namespace ShoppingFeed\Sdk\Api\Order\Identifier;

class Reference implements OrderIdentifier
{
    private $reference;

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
}
