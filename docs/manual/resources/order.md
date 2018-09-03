# Order

## Access

Accessing order API can be done from the store.

```php
<?php
$orderApi = $session->getMainStore()->getOrderApi();
```

## Retrieve orders

To retrieve order you can use those methods :

- `getAll()` : will retrieve all orders
- `getPages()` : will retrieve all pages of orders
- `getPage()` : will retrieve one page of orders

You can pass pagination and search criteria to `getPage` and `getPages` methods.  
`getAll` only accept filters as it handle pagination automatically.  
  
Here are the available criteria at your disposal :

- `page` : the page to retrieve or start from
- `limit` : the number of item per page you want to retrieve (up to a maximum define by the API)
- `filters` : an array of filters to filter orders by certain attributes
    - `status` : filter order by their status, multiple status are allowed. Status available are : created, 
    waiting_store_acceptance, refused, waiting_shipment, shipped, cancelled, refunded, partially_refunded, 
    partially_shipped
    - `acknowledgment`: filter orders by their acknowledgment status allow values are `acknowledged` or `unacknowledged`
    - `channel` : filter orders by the requested channel id
    - `tag` : retrieve orders linked to the requested tag
    - `since`: filter orders created since the given date time
    - `until`: filter orders created until the given date time

Examples :

```php
// Criteria used to query order API
$criteria = [
    'page'    => 1,  // first page
    'limit'   => 20, // 20 order per page
    'filters' => [
        'status'         => ['shipped', 'cancelled']  // we only want order with shipped or cancelled status
        'acknowledgment' => 'acknowledged'            // we only want order that have been acknowledged
        'channel'        => 123                       // we only want order from the channel 123
        'tag'            => 'test'                    // we only want order linked to 'test' tag
        'since'          => '2017-12-01T12:00:00'     // we only want order created since 2017-12-01 12:00:00
        'until'          => '2018-01-31T12:00:00'     // we only want order created until 2018-01-31 12:00:00
    ]
];

// Retrieve all orders shipped or cancelled
foreach($orderApi->getAll($criteria['filters']) as $order) {
    echo $order->getId();
}

// Retrieve all pages of orders shipped or cancelled
foreach($orderApi->getPages($criteria) as $orderCollection) {
    echo $orderCollection->count();
}

// Retrieve a page of orders shipped or cancelled
foreach($orderApi->getPage($criteria) as $order) {
    echo $order->getId();
}
```

## Operations

From order API you can then access all available operation :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->accept('ref3', 'amazon')
    ->refuse('ref4', 'amazon')
    ->ship('ref5', 'amazon')
    ->cancel('ref1', 'amazon');

$orderApi->execute($operation);
```

Operations allowed on existing order will always be accepted as they are treated asynchronously.  
When sending operation on order you will receive a collection of tickets corresponding to tasks in our system 
that will handle the requested operation.
With this ticket collection you will be able to find what ticket has been associated with the operation on an order.

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->accept('ref3', 'amazon')
    ->refuse('ref4', 'amazon')
    ->ship('ref5', 'amazon')
    ->cancel('ref3', 'amazon');

$tickets = $orderApi->execute($operation);

// Tickets to follow all acceptance tasks
$accepted = $ticketCollection->getAccepted();

// Ticket ID to follow 'ref3' cancelling task
$ticketId = $tickets->getCanceled('ref3')[0]->getId();
```

### Accept

The accept operation accept 3 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$reason` : The reason of the acceptance (eq: 'Why we accept the order') 

Example :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->accept('ref1', 'amazon')
    ->accept('ref2', 'amazon', 'Why we accept it');

$orderApi->execute($operation);
```

### Cancel

The cancel operation accept 3 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$reason` : The reason of the cancelling (eq: 'Why we cancel the order') 

Example :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->cancel('ref1', 'amazon')
    ->cancel('ref2', 'amazon', 'Why we accept it');

$orderApi->execute($operation);
```

### Refuse

The refuse operation accept 3 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$refund` : Item references to refund (eq: `['itemref1', 'itemref2']`) 

Example :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->refuse('ref1', 'amazon')
    ->refuse('ref2', 'amazon', ['itemref1', 'itemref2']);

$orderApi->execute($operation);
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
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->ship('ref1', 'amazon')
    ->ship('ref2', 'amazon', 'ups', '123456789abcdefg', 'http://tracking.url/');

$orderApi->execute($operation);
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
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->acknowledge('reference1', 'amazon', 'success', 'store-reference')
    ->acknowledge('reference1', 'amazon', 'error', 'store-reference')
    ->acknowledge('reference1', 'amazon', 'error', 'store-reference', 'Order well acknowledged');

$orderApi->execute($operation);
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
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->unacknowledge('reference1', 'amazon', 'success', 'store-reference')
    ->unacknowledge('reference1', 'amazon', 'error', 'store-reference')
    ->unacknowledge('reference1', 'amazon', 'error', 'store-reference', 'Order well unacknowledged');

$orderApi->execute($operation);
```
