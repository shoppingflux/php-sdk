<?php
namespace ShoppingFeed\Sdk\Http\Adapter;

use Psr\Http\Message;
use ShoppingFeed\Sdk\Client\ClientOptions;

/**
 * Http Client Adapter Interface
 *
 * This interface ensure that any adapter that implements it will be compatible with the SDK functioning
 * Adapter should check for their dependency existence
 *
 * @package ShoppingFeed\Sdk\Http\Adapter
 */
interface AdapterInterface
{
    /**
     * Configure current adapter with given options
     *
     * @param ClientOptions $options
     *
     * @return AdapterInterface
     */
    public function configure(ClientOptions $options);

    /**
     * Send a single HTTP request
     *
     * @param Message\RequestInterface $request Psr\RequestInterface object ready to be sent
     * @param array                    $options Options to pass to the http client
     *
     * @return null|Message\ResponseInterface
     */
    public function send(Message\RequestInterface $request, array $options = []);

    /**
     * Send multiples HTTP requests
     *
     * @param array $requests An array of Psr\RequestInterface object ready to be sent
     * @param array $options  Options to pass to the http client
     *
     * @return void
     */
    public function batchSend(array $requests, array $options = []);

    /**
     * Set header into the http client
     *
     * @param string|array $name  Can be the header name or a full set of headers array('Name' => 'Value',...)
     * @param string       $value Value in case of setting only one header
     *
     * @return AdapterInterface
     */
    public function addHeader($name, $value = null);

    /**
     * Create request from given parameters
     *
     * @param string $method  Http method
     * @param string $uri     The URI to call, ex: '/my/uri'
     * @param array  $headers An array of headers to add to the request, ex: array('MyHeader' => 'Its Content')
     * @param null   $body    The body as a string to send via the request
     *
     * @return Message\RequestInterface
     */
    public function createRequest($method, $uri, array $headers = [], $body = null);

    /**
     * Use the given token in the 'Authorization' header for all request sent via the adapter
     *
     * @param string $token
     *
     * @return AdapterInterface
     */
    public function withToken($token);
}
