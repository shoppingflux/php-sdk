<?php

namespace ShoppingFeed\Sdk\Client;

class ClientOptions
{
    private $baseUri = 'https://api.shopping-feed.com';

    private $version = 'v1';

    private $token;

    private $autoConnect = false;

    private $handleRateLimit = true;

    private $retryOnServerError = true;

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri): void
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken():? string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function autoConnect(): bool
    {
        return $this->autoConnect;
    }

    /**
     * @param bool $handle
     *
     * @return ClientOptions
     */
    public function setHandleRateLimit(bool $handle): self
    {
        $this->handleRateLimit = $handle;

        return $this;
    }

    public function handleRateLimit(): bool
    {
       return $this->handleRateLimit;
    }

    /**
     * @param bool $retry
     *
     * @return ClientOptions
     */
    public function setRetryOnServerError(bool $retry): self
    {
        $this->retryOnServerError = $retry;

        return $this;
    }

    public function retryOnServerError(): bool
    {
        return $this->retryOnServerError;
    }

    /**
     * @param bool $autoConnect
     *
     * @return ClientOptions
     */
    public function setAutoConnect(bool $autoConnect): self
    {
        $this->autoConnect = $autoConnect;

        return $this;
    }
}
