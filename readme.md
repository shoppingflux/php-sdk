# Welcome to the Shopping Feed PHP SDK


### Authentication against the API

The Shopping Feed API requires that you are authenticated to perform any calls.

In order to make authenticated call, you should build the client in that way:

```php
<?php
namespace ShoppingFeed\Sdk;

// Setup credentials to connect to the API, and create session
$credential = new Credential\Token('api-token');
$session    = Client\Client::createSession($credential);
```

### Accessing to your stores

```php
$store = $session->getMainStore();
$store->getName(); // test-store
$store->getId(); // 1276
// ... and so on
```

If you manage more than one store, you can use the store collection object

```php

// Get store collection
$stores = $session->getStores();
// Count the number of stores [int]
$stores->count();
// get particular store
$tores->select('id-or-store-name');
// Loop over available stores
foreach ($stores as $store) {
	$store->getName(); 
}
```

### SDK guides

- [Authentication in details](doc/authenticate.md)
- [Error handling and debug](doc/error-handling.md)

### SDK resources documentation

- [Inventiory management](doc/catalog.md)


### Generates XML compliant feed for import

The SDK is able to simplify XML feed creation by providing necessary tools.

```php
<?php
$generator = $client->createProductGenerator();
```

Check the documentation at https://github.com/shoppingflux/php-feed-generator to learn how to create compliant feed.

