<?php
namespace ShoppingFeed\Sdk\Guzzle\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ServerErrorHandler
{
    private const STATUS = [
        500 => true,
        502 => true,
        503 => true,
        504 => true
    ];

    /**
     * Retry x times the request before entering to error
     *
     * @var int
     */
    private $maxRetries = 10;

    /**
     * @param int                    $count
     * @param RequestInterface       $request
     * @param ResponseInterface|null $response
     *
     * @return bool
     */
    public function decide(int $count, RequestInterface $request, ResponseInterface $response = null): bool
    {
        if ($count >= $this->maxRetries) {
            return false;
        }

        return ($response && isset(self::STATUS[$response->getStatusCode()]));
    }
}
