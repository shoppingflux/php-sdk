# Catalog operations


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
