# Authenticate and create SDK session

The SDK offer two ways to authenticate against the API:

### Login / password


```php
<?php
namespace ShoppingFeed\Sdk;

$credential = new Credential\Password('login', 'password');
$session    = Client\Client::createSession($credential);
```

By login, we understand your **account name**, not store name or email address.

This method cost 2 calls to the API:

- 1 for getting your API token based on login / password info
- 1 To get your session details


### Token

This is the prefered way, because this method only perform 1 HTTP call

```php
<?php
namespace ShoppingFeed\Sdk;

$credential = new Credential\Token('api_token');
$session    = Client\Client::createSession($credential);
```


### Passing client options

Optionally, you can pass options when creating a new session with the static method.

```php
<?php
namespace ShoppingFeed\Sdk;

$options = new Client\ClientOptions();
$options->setHandleRateLimit(false);

$credential = new Credential\Token('api_token');
$session    = Client\Client::createSession($credential, $options);
```