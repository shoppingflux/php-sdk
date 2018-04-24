<?php
namespace ShoppingFeed\Sdk\Hal;

use GuzzleHttp as Guzzle;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ShoppingFeed\Sdk\Client\Client as SdkClient;

class HalClient
{
    /**
     * @var Guzzle\HandlerStack
     */
    private $stack;

    /**
     * @var Guzzle\Client
     */
    private $client;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @param string              $baseUri
     * @param Guzzle\HandlerStack $stack
     */
    public function __construct($baseUri, Guzzle\HandlerStack $stack = null)
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
        $stack->push(Guzzle\Middleware::mapRequest(function (RequestInterface $request) use ($token) {
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
        return new Guzzle\Psr7\Request($method, $uri, $headers, $body);
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
        $pool = new Guzzle\Pool($this->client, $requests, $config);
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
     * @param Guzzle\HandlerStack $stack
     */
    private function createClient(Guzzle\HandlerStack $stack = null)
    {
        if (null === $stack) {
            $stack = Guzzle\HandlerStack::create();
        }

        $this->stack  = $stack;
        $this->client = new Guzzle\Client([
            'handler'    => $this->stack,
            'base_uri'   => $this->baseUri,
            'headers'    => [
                'Accept'          => 'application/json',
                'User-Agent'      => 'SF-SDK-PHP/' . SdkClient::VERSION,
                'Accept-Encoding' => 'gzip',
            ]
        ]);
    }
}
