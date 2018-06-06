# inventories operations

### Read inventories

Accessing to the inventory API can be done from store resource

```php
<?php
$inventoryApi = $session->getMainStore()->getInventoryApi();
```

Find a inventory item by product's reference (sku):

```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Catalog\InventoryDomain $inventoryApi */
$item = $inventoryApi->getByReference('AZUR103-XL');
// Access the quantity as integer
$item->getQuantity();
// Access the reference
$item->getReference();
// Get last modification date time
$item->getUpdatedAt()->format('c');
```

Get a particular page of inventories

```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Catalog\InventoryDomain $inventoryApi */
$criteria = [
  'page' => 1,  
  'limit' => 10,  
];
foreach ($inventoryApi->getPage($criteria) as $inventory) {
	echo $inventory->getQuantity() . PHP_EOL;
}
```

Or Iterates over all inventories of your catalog

```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Catalog\InventoryDomain $inventoryApi */
foreach ($inventoryApi->getAll() as $inventory) {
	echo $inventory->getQuantity() . PHP_EOL;
}
```

### Update inventories


```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Catalog\InventoryDomain $inventoryApi */
$inventoryUpdate = new \ShoppingFeed\Sdk\Api\Catalog\InventoryUpdate();
$inventoryUpdate
    ->add('ref1', 7)
    ->add('ref2', 1);
$inventoryApi->execute($inventoryUpdate);
```

The collection object holds updated resources

```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Catalog\InventoryResource[] $collection */
// Retrieve the content of resources
foreach ($collection as $inventory) {
	echo $inventory->getId() . PHP_EOL;
	echo $inventory->getUpdatedAt()->format('c') . PHP_EOL;
}
```
