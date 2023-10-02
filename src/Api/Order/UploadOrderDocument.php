<?php

namespace ShoppingFeed\Sdk\Api\Order;

use ShoppingFeed\Sdk\Exception;

class UploadOrderDocument
{
    public const INVOICE = 'invoice';

    private const TYPES = [
        self::INVOICE,
    ];

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $type;

    public function __construct(
        string $path,
        string $type
    ) {
        if (! $this->isAllowedType($type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Only %s types are accepted',
                implode(', ', self::TYPES)
            ));
        }

        $this->path = $path;
        $this->type = $type;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    private function isAllowedType(string $type): bool
    {
        return in_array($type, self::TYPES);
    }
}
