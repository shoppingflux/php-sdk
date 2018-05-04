<?php
namespace ShoppingFeed\Sdk\Client;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Guzzle\Middleware as SfMiddleware;
use ShoppingFeed\Sdk\Credential\CredentialInterface;

class Client
{
    const VERSION = '1.0.0';

    /**
     * @var Hal\HalClient
     */
    private $client;

    /**
     * @param CredentialInterface $credential
     * @param ClientOptions|null  $options
     *
     * @return \ShoppingFeed\Sdk\Api\Session\SessionResource
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

        $this->client = new Hal\HalClient(
            $options->getBaseUri(),
            $this->createHandlerStack($options)
        );
    }

    /**
     * @return Hal\HalClient
     */
    public function getHalClient()
    {
        return $this->client;
    }

    /**
     * @return bool
     */
    public function ping()
    {
        return (bool) $this
            ->getHalClient()
            ->request('GET', 'v1/ping')
            ->getProperty('timestamp');
    }

    /**
     * @param CredentialInterface $credential
     *
     * @return \ShoppingFeed\Sdk\Api\Session\SessionResource
     */
    public function authenticate(CredentialInterface $credential)
    {
        return $credential->authenticate($this->getHalClient());
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
            $handler = new SfMiddleware\RateLimitHandler(3, $logger);
            $stack->push(Middleware::retry([$handler, 'decide'], [$handler, 'delay']));
        }

        $retryCount = $options->getRetryOnServerError();
        if ($retryCount) {
            $handler = new SfMiddleware\ServerErrorHandler($retryCount);
            $stack->push(Middleware::retry([$handler, 'decide']));
        }

        if ($logger) {
            $stack->push(Middleware::log($logger, new MessageFormatter()));
        }

        return $stack;
    }
}
