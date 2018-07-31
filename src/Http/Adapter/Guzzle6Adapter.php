<?php
namespace ShoppingFeed\Sdk\Http\Adapter;

use GuzzleHttp;
use Psr\Http\Message\RequestInterface;
use ShoppingFeed\Sdk\Client;
use ShoppingFeed\Sdk\Http;

class Guzzle6Adapter implements Http\Adapter\AdapterInterface
{
    /**
     * @var GuzzleHttp\HandlerStack
     */
    private $stack;

    /**
     * @var Client\ClientOptions
     */
    private $options;

    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    public function __construct(
        Client\ClientOptions $options,
        GuzzleHttp\HandlerStack $stack = null
    )
    {
        $this->options = $options;
        $this->stack   = $stack ?: $this->createHandlerStack($this->options);
        $this->client  = new GuzzleHttp\Client([
            'handler'  => $this->stack,
            'base_uri' => $this->options->getBaseUri(),
            'headers'  => [
                'Accept'          => 'application/json',
                'User-Agent'      => 'SF-SDK-PHP/' . Client\Client::VERSION,
                'Accept-Encoding' => 'gzip',
            ],
        ]);
    }

    /**
     * @inheritdoc
     *
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public function send(RequestInterface $request, array $options = [])
    {
        return $this->client->send($request, $options);
    }

    /**
     * @inheritdoc
     */
    public function batchSend(array $requests, array $options = [])
    {
        $pool = new GuzzleHttp\Pool($this->client, $requests, $options);
        $pool->promise()->wait(true);
    }

    /**
     * @inheritdoc
     */
    public function createRequest($method, $uri, array $headers = [], $body = null)
    {
        return new GuzzleHttp\Psr7\Request($method, $uri, $headers, $body);
    }

    /**
     * @inheritdoc
     */
    public function withToken($token)
    {
        $stack = clone $this->stack;
        $stack->push(GuzzleHttp\Middleware::mapRequest(function (RequestInterface $request) use ($token) {
            return $request->withHeader('Authorization', 'Bearer ' . trim($token));
        }));

        return $this->createClient($this->options->getBaseUri(), $stack);
    }

    /**
     * Create new adapter with given stack
     *
     * @param string                  $baseUri
     * @param GuzzleHttp\HandlerStack $stack
     *
     * @return Http\Adapter\AdapterInterface
     */
    private function createClient($baseUri, $stack)
    {
        $options = $this->options;
        $options->setBaseUri($baseUri);

        return new self($options, $stack);
    }

    /**
     * Create handler stack
     *
     * @param Client\ClientOptions $options
     *
     * @return GuzzleHttp\HandlerStack
     */
    private function createHandlerStack(Client\ClientOptions $options)
    {
        $stack  = GuzzleHttp\HandlerStack::create();
        $logger = $options->getLogger();

        if ($options->handleRateLimit()) {
            $handler = new Http\Middleware\RateLimitHandler(3, $logger);
            $stack->push(GuzzleHttp\Middleware::retry([$handler, 'decide'], [$handler, 'delay']));
        }

        $retryCount = $options->getRetryOnServerError();
        if ($retryCount) {
            $handler = new Http\Middleware\ServerErrorHandler($retryCount);
            $stack->push(GuzzleHttp\Middleware::retry([$handler, 'decide']));
        }

        if ($logger) {
            $stack->push(GuzzleHttp\Middleware::log($logger, new GuzzleHttp\MessageFormatter()));
        }

        return $stack;
    }
}
