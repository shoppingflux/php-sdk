# inventories operations

### Read inventories

Accessing to the inventory domain can be done from store resource

```php
$domain = $session->getMainStore()->getInventory();
```

Find a inventory item by product's reference (sku):

```php
$item = $domain->getByReference('AZUR103-XL');
// Access the quantity as integer
$item->getQuantity();
// Access the reference
$item->getReference();
// Get last modification date time
$item->getUpdatedAt()->format('c');
```

Get a particular page of inventories

```php
$page  = 1;
$limit = 20;
foreach ($domain->getPage($page, $limit) as $inventory) {
	echo $inventory->getQuantity() . PHP_EOL;
}
```

Or Iterates over all inventories of your catalog

```php
$page = 1;
foreach ($domain->getAll($page) as $inventory) {
	echo $inventory->getQuantity() . PHP_EOL;
}
```

### Update inventories


```php
$operation = new InventoryUpdate();
$operation->add('ref1', 7);
$operation->add('ref2', 1);

// Then run the operation
$collection = $domain->execute($operation);
```

The collection object holds updated resources

```php
// Retrieve the content of resources
foreach ($collection as $inventory) {
	echo $inventory->getId() . PHP_EOL;
	echo $inventory->getUpdatedAt()->format('c') . PHP_EOL;
)
```
