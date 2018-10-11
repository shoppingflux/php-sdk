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
     * @var string
     */
    private $userAgent = 'SF-SDK-PHP/' . Client::VERSION;

    /**
     * Name of the platform using the SDK
     *
     * @var string
     */
    private $platform;

    /**
     * Version of the platform using the SDK
     *
     * @var string
     */
    private $platformVersion;

    /**
     * @var array
     */
    private $headers = [
        'Accept'          => 'application/json',
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
        $this->buildUserAgentHeader();

        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return ClientOptions
     */
    public function addHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Add platform information for SDK user agent
     *
     * @param string $platform
     * @param string $version
     *
     * @return ClientOptions
     */
    public function setUserAgentDetails($platform, $version)
    {
        $this->platform        = $platform;
        $this->platformVersion = $version;

        return $this;
    }

    /**
     * Build user agent http header based on available information
     */
    private function buildUserAgentHeader()
    {
        $this->headers['User-Agent'] = $this->userAgent;
        if (! empty($this->platform)) {
            $this->headers['User-Agent'] .= " ($this->platform;$this->platformVersion)";
        }
    }
}
