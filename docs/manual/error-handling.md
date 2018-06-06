# Handle Rate-Limit and errors


### Rate-Limits

By default, the SDK handle gracefully 429 response from the API : When occurred, the SDK waits (sleep) until new calls are available, then retry the last failed request.

The SDK will fails the API respond consecutively 429 after 3 times.

This feature can be disabled when creating a API client. If disabled, it will be for all calls on any API point made with the client instance.

```php
<?php
namespace ShoppingFeed\Sdk;

$options = new Client\ClientOptions();
$options->setHandleRateLimit(false);
```

### Server errors

The SDK allow the possibility to retry requests when server fails to respond, in range of 5xx status codes. You can set how many time the request will be retried (with an exponential backoff)

```php
<?php
namespace ShoppingFeed\Sdk;

$options = new Client\ClientOptions();
$options->setRetryOnServerError(3);
```

### Debugging requests

If you want trace requests performed by the SDK, you can pass an instance of `Psr\loggerInterface`, which will log all request in `debug` level:

```php
<?php
namespace ShoppingFeed\Sdk;

$options = new Client\ClientOptions();
$options->setLogger($psrLogger);
```
