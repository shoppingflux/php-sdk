<?php
namespace ShoppingFeed\Sdk\Http\Adapter;

use Psr\Http\Message;

interface AdapterInterface
{
    /**
     * Send HTTP request
     *
     * @param Message\RequestInterface $request
     * @param array                    $options
     *
     * @return null|Message\ResponseInterface
     */
    public function send(Message\RequestInterface $request, array $options = []);

    /**
     * Send a batch of HTTP requests
     *
     * @param array $requests
     * @param array $options
     *
     * @return void
     */
    public function batchSend(array $requests, array $options = []);

    /**
     * Create request from given parameters
     *
     * @param string $method Http method
     * @param string $uri
     * @param array  $headers
     * @param null   $body
     *
     * @return Message\RequestInterface
     */
    public function createRequest($method, $uri, array $headers = [], $body = null);

    /**
     * Create client with given token
     *
     * @param string $token
     *
     * @return AdapterInterface
     */
    public function withToken($token);
}
