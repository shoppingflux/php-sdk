<?php
namespace ShoppingFeed\Sdk\Guzzle\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class LogRequestHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    public function __invoke(RequestInterface $request)
    {
        $this->logger->debug(
            sprintf('%s : %s', $request->getMethod(), $request->getUri()->__toString())
        );

        return $request;
    }
}
