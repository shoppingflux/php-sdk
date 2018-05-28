# Order

## Access

Accessing order operation can be done from the store.

```php
<?php
$orderApi = $session->getMainStore()->getOrderApi();
```

## Operations

From order API you can then access all available operation :
```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Order\OrderDomain $orderApi */
$operations = new \ShoppingFeed\Sdk\Order\OrderOperation();
$operations
    ->accept('ref3', 'amazon')
    ->refuse('ref4', 'amazon')
    ->ship('ref5', 'amazon')
    ->cancel('ref1', 'amazon')
    ->execute($orderApi->getLink());
```

### Accept

The accept operation accept 3 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$reason` : The reason of the acceptance (eq: 'Why we accept the order') 

Example :
```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Order\OrderDomain $orderApi */
$operations = new \ShoppingFeed\Sdk\Order\OrderOperation();
$operations
    ->accept('ref1', 'amazon')
    ->accept('ref2', 'amazon', 'Why we accept it')
    ->execute($orderApi->getLink());
```

### Cancel

The cancel operation accept 3 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$reason` : The reason of the cancelling (eq: 'Why we cancel the order') 

Example :
```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Order\OrderDomain $orderApi */
$operations = new \ShoppingFeed\Sdk\Order\OrderOperation();
$operations
    ->cancel('ref1', 'amazon')
    ->cancel('ref2', 'amazon', 'Why we accept it')
    ->execute($orderApi->getLink());
```

### Refuse

The refuse operation accept 3 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$refund` : Item references to refund (eq: `['itemref1', 'itemref2']`) 

Example :
```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Order\OrderDomain $orderApi */
$operations = new \ShoppingFeed\Sdk\Order\OrderOperation();
$operations
    ->refuse('ref1', 'amazon')
    ->refuse('ref2', 'amazon', ['itemref1', 'itemref2'])
    ->execute($orderApi->getLink());
```

### Ship

The ship operation accept 3 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$carrier` : The carrier name used for the shipment (eq: 'ups') 
3. [optional] `$trackingNumber` : Tracking number (eq: '01234598abcdef') 
3. [optional] `$trackingLink` : Tracking link (eq: 'http://tracking.url/') 

Example :
```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Order\OrderDomain $orderApi */
$operations = new \ShoppingFeed\Sdk\Order\OrderOperation();
$operations
    ->ship('ref1', 'amazon')
    ->ship('ref2', 'amazon', 'ups', '123456789abcdefg', 'http://tracking.url/')
    ->execute($orderApi->getLink());
```