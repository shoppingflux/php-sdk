<?php
namespace ShoppingFeed\Sdk\Guzzle\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This handler manage API rate limits by mae the client entering to sleep,
 * Then retry the last query if the API returns a 429 response status code
 */
class RateLimitHandler
{
    /**
     * Retry 3 times the request before entering to error
     *
     * @var int
     */
    private $maxRetries = 3;

    /**
     * @param int                    $count
     * @param RequestInterface       $request
     * @param ResponseInterface|null $response
     *
     * @return bool
     */
    public function decide(int $count, RequestInterface $request, ResponseInterface $response = null): bool
    {
        if (! $response || $count >= $this->maxRetries) {
            return false;
        }

        return $response->getStatusCode() === 429;
    }

    /**
     * @param int               $count    Number of retries
     * @param ResponseInterface $response
     *
     * @return int milliseconds to wait before next call
     */
    public function delay($count, ResponseInterface $response): int
    {
        return (int) ceil($response->getHeaderLine('X-RateLimit-Wait') * 1000);
    }
}
