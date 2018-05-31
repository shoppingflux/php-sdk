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
$updateOperation = new \ShoppingFeed\Sdk\Order\OrderOperation();
$updateOperation
    ->accept('ref3', 'amazon')
    ->refuse('ref4', 'amazon')
    ->ship('ref5', 'amazon')
    ->cancel('ref1', 'amazon');
$orderApi->execute($updateOperation);
```

Operations allowed on existing order will always be accepted as they are treated asynchronously.  
When sending operation on order you will receive a collection of tickets corresponding to tasks in our system 
that will handle the requested operation.
With this ticket collection you will be able to find what ticket has been associated with the operation on an order.

```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Order\OrderDomain $orderApi */
$updateOperation = new \ShoppingFeed\Sdk\Order\OrderOperation();
/** @var \ShoppingFeed\Sdk\Api\Order\OrderTicketCollection $ticketCollection */
$ticketCollection = $updateOperation
    ->accept('ref3', 'amazon')
    ->refuse('ref4', 'amazon')
    ->ship('ref5', 'amazon')
    ->cancel('ref3', 'amazon');
$orderApi->execute($updateOperation);

// Ticket ID to follow 'ref3' acceptance task
$ticketId = $ticketCollection->getAcceptedTicket('ref3')->getId();

// Ticket ID to follow 'ref3' cancelling task
$ticketId = $ticketCollection->getCanceledTicket('ref3')->getId();
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
$updateOperation = new \ShoppingFeed\Sdk\Order\OrderOperation();
$updateOperation
    ->accept('ref1', 'amazon')
    ->accept('ref2', 'amazon', 'Why we accept it');
$orderApi->execute($updateOperation);
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
$updateOperation = new \ShoppingFeed\Sdk\Order\OrderOperation();
$updateOperation
    ->cancel('ref1', 'amazon')
    ->cancel('ref2', 'amazon', 'Why we accept it');
$orderApi->execute($updateOperation);
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
$updateOperation = new \ShoppingFeed\Sdk\Order\OrderOperation();
$updateOperation
    ->refuse('ref1', 'amazon')
    ->refuse('ref2', 'amazon', ['itemref1', 'itemref2']);
$orderApi->execute($updateOperation);
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
$updateOperation = new \ShoppingFeed\Sdk\Order\OrderOperation();
$updateOperation
    ->ship('ref1', 'amazon')
    ->ship('ref2', 'amazon', 'ups', '123456789abcdefg', 'http://tracking.url/');
$orderApi->execute($updateOperation);
```
### Acknowledge

To acknowledge the good reception of order :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [mandatory] `$status` : Status of acknowledgment (eq: 'success') 
4. [mandatory] `$storeReference` : Store reference (eq: 'store-reference') 
5. [optional] `$message` : Acknowledge message  (eq: 'Order well acknowledge') 

Example :
```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Order\OrderDomain $orderApi */
$updateOperation = new \ShoppingFeed\Sdk\Order\OrderOperation();
$updateOperation
    ->acknowledge('reference1', 'amazon', 'success', 'store-reference')
    ->acknowledge('reference1', 'amazon', 'error', 'store-reference')
    ->acknowledge('reference1', 'amazon', 'error', 'store-reference', 'Order well acknowledged');
$orderApi->execute($updateOperation);
```

### Unacknowledge

To unacknowledge the good reception of order :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [mandatory] `$status` : Status of unacknowledgment (eq: 'success') 
4. [mandatory] `$storeReference` : Store reference (eq: 'store-reference') 
5. [optional] `$message` : Unacknowledge message  (eq: 'Order well unacknowledge') 

Example :
```php
<?php
/** @var \ShoppingFeed\Sdk\Api\Order\OrderDomain $orderApi */
$updateOperation = new \ShoppingFeed\Sdk\Order\OrderOperation();
$updateOperation
    ->unacknowledge('reference1', 'amazon', 'success', 'store-reference')
    ->unacknowledge('reference1', 'amazon', 'error', 'store-reference')
    ->unacknowledge('reference1', 'amazon', 'error', 'store-reference', 'Order well unacknowledged');
$orderApi->execute($updateOperation);
```
