<?php
namespace ShoppingFeed\Sdk\Client;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Jsor\HalClient;
use ShoppingFeed\Feed\ProductGenerator;
use ShoppingFeed\Sdk\Credential\CredentialInterface;
use ShoppingFeed\Sdk\Guzzle\Middleware\LogRequestHandler;
use ShoppingFeed\Sdk\Guzzle\Middleware\RateLimitHandler;
use ShoppingFeed\Sdk\Guzzle\Middleware\ServerErrorHandler;

class Client
{
    /**
     * @var HalClient\HalClient
     */
    private $client;

    /**
     * @param CredentialInterface $credential
     * @param ClientOptions|null  $options
     *
     * @return \ShoppingFeed\Sdk\Session\SessionResource
     */
    public static function createSession(CredentialInterface $credential, ClientOptions $options = null)
    {
        return (new self($options))->authenticate($credential);
    }

    /**
     * @param ClientOptions $options
     */
    public function __construct(ClientOptions $options = null)
    {
        if (null === $options) {
            $options = new ClientOptions();
        }

        $this->configureHttpClient($options);
    }

    /**
     * @return bool
     */
    public function ping()
    {
        $resource = $this->client->get('v1/ping');

        return (bool) $resource->getProperty('timestamp');
    }

    /**
     * @param CredentialInterface $credential
     *
     * @return \ShoppingFeed\Sdk\Session\SessionResource
     */
    public function authenticate(CredentialInterface $credential)
    {
        return $credential->authenticate($this->client);
    }

    /**
     * @return ProductGenerator
     */
    public function createProductGenerator()
    {
        return new ProductGenerator();
    }

    /**
     * @param ClientOptions $options
     */
    private function configureHttpClient(ClientOptions $options)
    {
        $client        = new \GuzzleHttp\Client(['handler' => $this->createHandlerStack($options)]);
        $client        = new HalClient\HttpClient\Guzzle6HttpClient($client);
        $client        = new HalClient\HalClient($options->getBaseUri(), $client);
        $this->client  = $client;
    }

    /**
     * @param ClientOptions $options
     *
     * @return HandlerStack
     */
    private function createHandlerStack(ClientOptions $options)
    {
        $stack  = HandlerStack::create();
        $logger = $options->getLogger();

        if ($options->handleRateLimit()) {
            $handler = new RateLimitHandler(3, $logger);
            $stack->push(Middleware::retry([$handler, 'decide'], [$handler, 'delay']));
        }

        $retryCount = $options->getRetryOnServerError();
        if ($retryCount) {
            $handler = new ServerErrorHandler($retryCount);
            $stack->push(Middleware::retry([$handler, 'decide']));
        }

        if ($logger) {
            $stack->push(Middleware::mapRequest(new LogRequestHandler($logger)));
        }

        return $stack;
    }
}
