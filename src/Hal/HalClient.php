<?php
namespace ShoppingFeed\Sdk\Hal;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HalClient
{
    /**
     * @var HandlerStack
     */
    private $stack;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @param              $baseUri
     * @param HandlerStack $stack
     */
    public function __construct($baseUri, HandlerStack $stack = null)
    {
        $this->baseUri = $baseUri;
        $this->createClient($stack);
    }

    /**
     * @param $token
     *
     * @return HalClient
     */
    public function withToken($token)
    {
        $stack = clone $this->stack;
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) use ($token) {
            return $request->withHeader('Authorization', 'Bearer ' . trim($token));
        }));

        $instance = clone $this;
        $instance->createClient($stack);

        return $instance;
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @param array  $headers
     * @param null   $body
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri, array $headers = [], $body = null)
    {
        return new Request($method, $uri, $headers, $body);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     *
     * @return null|HalResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, array $options = [])
    {
        return $this->send(
            $this->createRequest($method, $uri),
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
        $pool = new Pool($this->client, $requests, $config);
        $pool->promise()->wait(true);
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return null|HalResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(RequestInterface $request, array $options = [])
    {
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
        return HalResource::fromArray(
            $this,
            \GuzzleHttp\json_decode($response->getBody(), true)
        );
    }

    /**
     * @param HandlerStack $stack
     */
    private function createClient(HandlerStack $stack = null)
    {
        if (null === $stack) {
            $stack = HandlerStack::create();
        }

        $this->stack  = $stack;
        $this->client = new Client([
            'handler'  => $this->stack,
            'base_uri' => $this->baseUri,
            'headers'  => [
                'Accept' => 'application/json'
            ]
        ]);
    }
}
