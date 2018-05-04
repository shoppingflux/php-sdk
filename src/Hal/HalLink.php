<?php
namespace ShoppingFeed\Sdk\Hal;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\UriTemplate;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HalLink
{
    /**
     * @var UriTemplate
     */
    private static $uriTemplate;

    /**
     * @var string
     */
    private $href;

    /**
     * @var bool
     */
    private $templated;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $title;

    /**
     * @var HalClient
     */
    private $client;

    /**
     * @param HalClient $client
     * @param           $href
     * @param array     $config
     */
    public function __construct(HalClient $client, $href, array $config = [])
    {
        $this->href   = $href;
        $this->client = $client;

        if (isset($config['templated'])) {
            $this->templated = (bool) $config['templated'];
        }
        if (isset($config['type'])) {
            $this->type = $config['type'];
        }
        if (isset($config['name'])) {
            $this->name = $config['name'];
        }
        if (isset($config['title'])) {
            $this->title = $config['title'];
        }
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return bool
     */
    public function isTemplated()
    {
        return $this->templated;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param array $variables
     *
     * @return null|string|string[]
     */
    public function getUri(array $variables)
    {
        if (! $this->isTemplated()) {
            return $this->getHref();
        }

        if (null === static::$uriTemplate) {
            static::$uriTemplate = new UriTemplate();
        }

        return static::$uriTemplate->expand($this->getHref(), $variables);
    }

    /**
     * @param array $variables
     * @param array $options
     *
     * @return null|HalResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(array $variables = [], array $options = [])
    {
        return $this->client->send(
            $this->createRequest('GET', $variables),
            $options
        );
    }

    /**
     * @param mixed $data
     * @param array $variables
     * @param array $options
     *
     * @return null|HalResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put($data, array $variables = [], array $options = [])
    {
        return $this->client->send(
            $this->createRequest('PUT', $variables, $data),
            $options
        );
    }

    /**
     * @param mixed $data
     * @param array $variables
     * @param array $options
     *
     * @return null|HalResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch($data, array $variables = [], array $options = [])
    {
        return $this->client->send(
            $this->createRequest('PATCH', $variables, $data),
            $options
        );
    }

    /**
     * @param mixed $data
     * @param array $variables
     * @param array $options
     *
     * @return null|HalResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($data, array $variables = [], array $options = [])
    {
        return $this->client->send(
            $this->createRequest('POST', $variables, $data),
            $options
        );
    }

    /**
     * @param mixed $data
     * @param array $variables
     * @param array $options
     *
     * @return null|HalResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($data, array $variables = [], array $options = [])
    {
        return $this->client->send(
            $this->createRequest('DELETE', $variables, $data),
            $options
        );
    }

    /**
     * @param          $requests
     * @param callable $success
     * @param callable $error
     * @param array    $options
     * @param int      $concurrency
     *
     * @return void
     */
    public function batchSend(
        $requests,
        callable $success = null,
        callable $error = null,
        array $options = [],
        $concurrency = 10
    )
    {
        $config['concurrency'] = (int) $concurrency;
        $config['fulfilled']   = $this->createResponseCallback($success);
        $config['rejected']    = $this->createExceptionCallback($error);

        if ($options) {
            $config['options'] = $options;
        }

        $this->client->batchSend($requests, $config);
    }

    /**
     * @param RequestInterface|RequestInterface[] $request
     * @param array                               $config
     *
     * @return null|HalResource
     */
    public function send($request, array $config = [])
    {
        return $this->client->send($request, $config);
    }

    /**
     * @param       $method
     * @param array $variables
     * @param array $body
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function createRequest($method, array $variables = [], $body = null)
    {
        $uri     = $this->getUri($variables);
        $method  = strtoupper($method);
        $headers = [];

        if (! isset($headers['Content-Type']) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $headers['Content-Type'] = 'application/json';
            $body                    = \GuzzleHttp\json_encode($body);
        }

        return $this->client->createRequest($method, $uri, $headers, $body);
    }

    /**
     * @param callable|null $callback
     *
     * @return \Closure
     */
    private function createResponseCallback(callable $callback = null)
    {
        return function (ResponseInterface $response) use ($callback) {
            if ($callback) {
                $resource = $this->client->createResource($response);
                call_user_func($callback, $resource);
            }
        };
    }

    /**
     * @param callable|null $callback
     *
     * @return \Closure
     */
    private function createExceptionCallback(callable $callback = null)
    {
        return function (RequestException $exception) use ($callback) {
            if ($exception->hasResponse() && $callback) {
                $resource = $this->client->createResource($exception->getResponse());
                call_user_func($callback, $resource);
            }
        };
    }
}
