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

### Handle Rate-Limit and errors

By default, the SDK handle gracefully 429 response from the API : When occurred, the SDK waits until new calls are avaible, then retry the last failed request.

This feature can be disabled when creating a API client. If disabled, it will be for all calls on any API point made with the client instance

```php
$options = new ShoppingFeed\ApiSdk\ClientOptions();
$options->handleRateLimit(false);
```

### Accessing to your primary store

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


### Update inventories

The SDK will silently break call according the max limit
of items per call allowed by the API

```php
$operation = new InventoryUpdate();
$operation->add('ref1', 7);
$operation->add('ref2', 1);

// Optional:  Determine the number of items per request
$operation->setBatchSize(50);

// Then run the operation
$result = $session->getMainStore()->execute($operation);
```

The result object hold updated resources, and eventual errors

```php
// Check if one of the batch fails
$result->hasError(); // true

// Check if all batch failed
$result->isError(); // false

// Retrieve the content of resources
foreach ($result->getResource() as $inventory) {
	echo $inventory->getId() . PHP_EOL;
	echo $inventory->getUpdatedAt()->format('c') . PHP_EOL;
)
```

### Generates XML compliant feed for import

The SDK is able to simplify XML feed creation by providing necessary tools.

```php
<?php
$generator = $client->createProductGenerator();
```

Check the documentation at https://github.com/shoppingflux/php-feed-generator to learn how to create compliant feed.
