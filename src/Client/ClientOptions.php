<?php
namespace ShoppingFeed\Sdk\Client;

use Psr\Log\LoggerInterface;
use ShoppingFeed\Sdk\Http\Adapter\AdapterInterface;

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
     * @var AdapterInterface
     */
    private $httpAdapter;

    /**
     * @var array
     */
    private $headers = [
        'Accept'          => 'application/json',
        'User-Agent'      => 'SF-SDK-PHP/' . Client::VERSION,
        'Accept-Encoding' => 'gzip',
    ];

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
     * @return ClientOptions
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

    /**
     * @return AdapterInterface
     */
    public function getHttpAdapter()
    {
        return $this->httpAdapter;
    }

    /**
     * @param AdapterInterface $httpAdapter
     *
     * @return ClientOptions
     */
    public function setHttpAdapter(AdapterInterface $httpAdapter)
    {
        $this->httpAdapter = $httpAdapter;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return ClientOptions
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }
}
