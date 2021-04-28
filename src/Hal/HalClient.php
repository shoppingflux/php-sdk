<?php
namespace ShoppingFeed\Sdk\Hal;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ShoppingFeed\Sdk\Http\Adapter\AdapterInterface;
use ShoppingFeed\Sdk\Resource\Json;

class HalClient
{
    /**
     * @var AdapterInterface
     */
    private $client;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var null|int
     */
    private $transactionId = null;

    /**
     * @param AdapterInterface $httpClient
     */
    public function __construct($baseUri, AdapterInterface $httpClient)
    {
        $this->baseUri = $baseUri;
        $this->client  = $httpClient;
    }

    /**
     * @param string $token
     *
     * @return HalClient
     */
    public function withToken($token)
    {
        return new self(
            $this->baseUri,
            $this->client->withToken($token)
        );
    }

    /**
     * @param int $transactionId
     *
     * @return HalClient
     */
    public function withTransactionId($transactionId)
    {
        $instance                = clone $this;
        $instance->transactionId = $transactionId;

        return $instance;
    }

    /**
     * @param string      $method
     * @param string      $uri
     * @param array       $headers
     * @param null|string $body
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri, array $headers = [], $body = null)
    {
        return $this->client->createRequest($method, $uri, $headers, $body);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     *
     * @return null|HalResource
     */
    public function request($method, $uri, array $options = [])
    {
        $this->injectTransactionId($options);

        return $this->send(
            $this->client->createRequest($method, $uri),
            $options
        );
    }

    /**
     * @param       $requests
     * @param array $config
     *
     * @return void
     */
    public function batchSend($requests, array $config = [])
    {
        if (! isset($config['options'])) {
            $config['options'] = [];
        }

        $this->injectTransactionId($config['options']);

        $this->client->batchSend($requests, $config);
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return null|HalResource
     */
    public function send(RequestInterface $request, array $options = [])
    {
        $this->injectTransactionId($options);

        $response = $this->client->send($request, $options);
        if ($response instanceof ResponseInterface) {
            return $this->createResource($response);
        }

        return null;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return HalResource
     */
    public function createResource(ResponseInterface $response)
    {
        $data = [];
        $body = trim($response->getBody());

        if ($body) {
            $data = Json::decode($body, true);
        }

        return HalResource::fromArray($this, $data);
    }

    /**
     * Get Http adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->client;
    }

    /**
     * Inject transaction ID in query param of the request is one is available
     *
     * @param array &$options Request options to inject tid query param into
     */
    private function injectTransactionId(&$options)
    {
        if (null !== $this->transactionId) {
            if (! isset($options['query'])) {
                $options['query'] = [];
            }

            $options['query']['tid'] = $this->transactionId;
        }
    }
}
