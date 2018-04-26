<?php
namespace ShoppingFeed\Sdk\Client;

use Psr\Log\LoggerInterface;

class ClientOptions
{
    /**
     * @var bool
     */
    private $baseUri = 'https://api.shopping-feed.com';

    /**
     * @var bool
     */
    private $handleRateLimit = true;

    /**
     * @var int The number of retries before abandon 5xx requests
     */
    private $retryOnServerError = 0;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return ClientOptions
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $baseUri
     *
     * @return ClientOptions
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = trim($baseUri);

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @param bool $handle
     *
     * @return ClientOptions
     */
    public function setHandleRateLimit($handle)
    {
        $this->handleRateLimit = (bool) $handle;

        return $this;
    }

    /**
     * @return bool
     */
    public function handleRateLimit()
    {
        return $this->handleRateLimit;
    }

    /**
     * @param int $retryCount
     *
     * @return $this
     */
    public function setRetryOnServerError($retryCount)
    {
        $this->retryOnServerError = (int) $retryCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getRetryOnServerError()
    {
        return $this->retryOnServerError;
    }
}
