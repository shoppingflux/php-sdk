<?php
namespace ShoppingFeed\Sdk\Client;

use GuzzleHttp\HandlerStack;
use Jsor\HalClient;
use ShoppingFeed\Feed\ProductGenerator;
use ShoppingFeed\Sdk\Guzzle\Middleware\RateLimitHandler;
use ShoppingFeed\Sdk\Guzzle\Middleware\ServerErrorHandler;
use ShoppingFeed\Sdk\Store;

class Client
{
    /**
     * @var HalClient\HalClient
     */
    private $client;

    /**
     * @var HalClient\HalResource
     */
    private $profile;

    /**
     * @var string
     */
    private $version;

    /**
     * @param ClientOptions $options
     */
    public function __construct(ClientOptions $options)
    {
        $this->configureHttpClient($options);
        if ($options->autoConnect()) {
            $this->connect();
        }
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        $resource = $this->client->get('v1/ping');

        return (bool) $resource->getProperty('timestamp');
    }

    /**
     * @return Store\StoreResource
     */
    public function getMainStore(): ? Store\StoreResource
    {
        $resource = $this->connect()->profile->getFirstResource('store');
        if ($resource) {
            return new Store\StoreResource($resource, true);
        }
    }

    /**
     * @param bool $refresh
     *
     * @return Client
     */
    public function connect(bool $refresh = false): self
    {
        if (true === $refresh) {
            $this->profile = null;
        }

        if (null === $this->profile) {
            $this->profile = $this->client->get($this->version . '/me');
        }

        return $this;
    }

    /**
     * @return Store\StoreCollection
     */
    public function getStores(): Store\StoreCollection
    {
        return new Store\StoreCollection(
            $this->connect()->profile->getResource('store')
        );
    }

    /**
     * @param int|string $idOrName
     *
     * @return Store\StoreResource
     */
    public function selectStore($idOrName): Store\StoreResource
    {
        $stores = $this->connect()->getStores();

        if (ctype_digit($idOrName)) {
            return $stores->getById($idOrName);
        }

        return $stores->getByName($idOrName);
    }

    /**
     * @return ProductGenerator
     */
    public function createProductGenerator(): ProductGenerator
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
        $this->client  = $client->withHeader('Authorization', 'Bearer ' . $options->getToken());
        $this->version = $options->getVersion();
    }

    /**
     * @param ClientOptions $options
     *
     * @return HandlerStack
     */
    private function createHandlerStack(ClientOptions $options): HandlerStack
    {
        $stack = HandlerStack::create();

        if ($options->handleRateLimit()) {
            $handler = new RateLimitHandler;
            $stack->push(\GuzzleHttp\Middleware::retry([$handler, 'decide'], [$handler, 'delay']));
        }

        if ($options->retryOnServerError()) {
            $handler = new ServerErrorHandler();
            $stack->push(\GuzzleHttp\Middleware::retry([$handler, 'decide']));
        }

        return $stack;
    }
}
