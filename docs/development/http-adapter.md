Http Adapter
============

The shopping flux SDK comes with an adapter to use Guzzle6 Http client library.
But you can develop your own adapter if need be.

Integration
-----------
  
To use your own http client you just have to develop the proper adapter which should implement `Http\Adapter\AdapterInterface`.
Then declare it as an option you pass to the client :  

```php
$customerAdapter = new YourCustomAdapter();

$clientOptions = new \ShoppingFeed\Sdk\Client\ClientOptions();
$clientOptions->setHttpAdapter($customerAdapter);

$client  = new \ShoppingFeed\Sdk\Client\Client($clientOptions);
```

Adapter Creation
----------------

To develop your own adapter create a new class implementing `Http\Adapter\AdapterInterface` with those methods :

| Method | Parameters | Description |
|--------|------------|-------------|
| `send` | <ul><li>`Psr\RequestInterface $request`</li><li>`array $options = []`</li></ul> | Handle the http call of the given request. |
| `batchSend` | <ul><li>`array $requests`</li><li>`array $options = []`</li></ul> | Send multiple requests |
| `createRequest` | <ul><li>`string $method`</li><li>`string $uri`</li><li>`array $headers = []`</li><li>`string $body = null`</li></ul> | Create a request from given parameters |
| `withToken` | <ul><li>`string $token`</li><li>`$stack`</li></ul> | Create a new instance of the adapter that use the token for identification |
