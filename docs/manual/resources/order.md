# Order

## Access

Accessing the order API can be done from your sessions. As your session can be 
linked to multiple stores, you have to select stores you want to access to.

### Single store management
```php
$storeId  = 1234;
$orderApi = $session->selectStore($storeId)->getOrderApi();
```

### Multiple stores management

```php
$storesIds = [1234, 5678];

foreach ($storesIds as $storeId) {
    $orderApi = $session->selectStore($storeId)->getOrderApi();
}
```

Please note that whole order API is store scoped. You cannot access an order 
from the wrong store even if your session is allowed to access the given 
order's store.

For instance, if your session has access to stores `1` and `5`, and if store 
`1` has an order `1111` and store `5` has an order `5555`, you cannot access 
order `5555` from store `1`:

```php
// Lead to a 404
$session->selectStore(1)->getOrderApi()->getOne(5555);
```

### Deprecated method

Usage of getMainStore() has been deprecated and SHOULD NOT be used anymore. 
It can lead to major issue if your session get access to new stores.
It will be removed in the next major version of the SDK.

```php
$storeId  = 1276;
$orderApi = $session->selectStore($storeId)->getOrderApi();
```

## Retrieve orders

To retrieve orders, you can use these methods :

- `getAll()` : will retrieve all orders
- `getPages()` : will retrieve all pages of orders
- `getPage()` : will retrieve one page of orders

You can pass pagination and search criteria to `getPage` and `getPages` methods.  
`getAll` only accepts filters as it handles pagination automatically.  
  
Here are the available criteria at your disposal :

- `page` : the page to retrieve or start from
- `limit` : the number of items per page you want to retrieve (up to a maximum defined by the API)
- `filters` : an array of filters to filter the orders by certain attributes
    - `status` : filter orders by their status, multiple status are allowed. Status available are : created, 
    waiting_store_acceptance, refused, waiting_shipment, shipped, cancelled, refunded, partially_refunded, 
    partially_shipped
    - `acknowledgment`: filters orders by their acknowledgment status. Allowed values are `acknowledged` or `unacknowledged`
    - `channel` : filters orders by the requested channel id
    - `tag` : retrieves orders linked to the requested tag
    - `since`: filters orders created since the given date time
    - `until`: filters orders created until the given date time

Examples :

```php
// Criteria used to query order API
$criteria = [
    'page'    => 1,  // first page
    'limit'   => 20, // 20 orders per page
    'filters' => [
        'status'         => ['shipped', 'cancelled']  // we only want orders with shipped or cancelled status
        'acknowledgment' => 'acknowledged'            // we only want orders that have been acknowledged
        'channel'        => 123                       // we only want orders from the channel 123
        'tag'            => 'test'                    // we only want orders linked to 'test' tag
        'since'          => '2017-12-01T12:00:00'     // we only want orders created since (after) 2017-12-01 12:00:00
        'until'          => '2018-01-31T12:00:00'     // we only want orders created until (before) 2018-01-31 12:00:00
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

From order API you can then access all available operations :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->accept('ref3', 'amazon')
    ->refuse('ref4', 'amazon')
    ->ship('ref5', 'amazon')
    ->cancel('ref1', 'amazon');

$orderApi->execute($operation);
```

Operations allowed on existing orders will always be accepted, as they are treated asynchronously.    
When sending operations on an order, you will receive batch IDs. 
See [ticket documentation](ticket.md) for more information on how to retrieve ticket and batch information.  

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->accept('ref3', 'amazon')
    ->refuse('ref4', 'amazon')
    ->ship('ref5', 'amazon')
    ->cancel('ref3', 'amazon');
    ->refund('ref6', 'amazon');

$result = $orderApi->execute($operation);

// get the list of batch ids
$ids = $result->getBatchIds(); // ['abc', 'def']

// Fetch all tickets generated for the operation
foreach ($result->getTickets() as $ticket) {
    $ticket->getId();
    $ticket->getStatus();
}

// Alternatively, you can wait until all ticket are processed
// do not forget to define a timeout to prevent script to be blocked
foreach ($result->wait(60)->getTickets() as $ticket) {
    $ticket->getId();
    $ticket->getStatus();
}
```

### Accept

The accept operation accepts 3 parameters :
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

The cancel operation accepts 3 parameters :
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

The refuse operation accepts 3 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 

Example :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->refuse('ref1', 'amazon');

$orderApi->execute($operation);
```

### Ship

The ship operation accepts 3 parameters :

1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$carrier` : The carrier name used for the shipment (eq: 'ups') 
4. [optional] `$trackingNumber` : Tracking number (eq: '01234598abcdef') 
5. [optional] `$trackingLink` : Tracking link (eq: 'http://tracking.url/') 
6. [optional] `$items` : Array of order item id and quantity to be shipped (eq: [['id' => 1234, 'quantity' => 1]]) 

Example :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->ship('ref1', 'amazon')
    ->ship('ref2', 'amazon', 'ups', '123456789abcdefg', 'http://tracking.url/');

$orderApi->execute($operation);
```

### Refund

The refund operation accepts 4 parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$shipping` : true if shipping cost need to be refunded, false otherwise (eq: `false`) 
4. [optional] `$products` : Item references and their quantities to refund (eq: `['itemref1' => 1, 'itemref2' => 2]`) 

Example :

```php
$products = [
  [
      'reference' => 'abc123',
      'quantity'  => 1,
  ],
  [
      'reference' => 'abc456',
      'quantity'  => 2,
  ],
];
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->refund('ref1', 'amazon')
    ->refund('ref2', 'amazon', true, $products);

$orderApi->execute($operation);
```

### Acknowledge

To acknowledge the reception of an order, you need the following parameters :

1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon') 
3. [optional] `$storeReference` : Store reference (eg: 'store-reference')
4. [optional] `$status` : Status of acknowledgment (eg: 'success' or 'error')
5. [optional] `$message` : In case or error status, you can provide a message (eg: 'Unknown product #ABC-123') 

Example :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->acknowledge('ref1', 'amazon', 'store-reference', 'success')
    ->acknowledge('ref2', 'amazon', 'store-reference', 'error')
    ->acknowledge('ref3', 'amazon', 'store-reference', 'error', 'Order well acknowledged');

$orderApi->execute($operation);
```

### Unacknowledge

To unacknowledge the reception of an order previously acknowledged, you need the following parameters :

1. [mandatory] `$reference` : Order reference (eg: 'reference1') 
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon')

Example :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->unacknowledge('ref1', 'amazon')
    ->unacknowledge('ref2', 'amazon')
    ->unacknowledge('ref3', 'amazon');

$orderApi->execute($operation);
```

### Upload documents

To upload order documents, you need the following parameters :
1. [mandatory] `$reference` : Order reference (eg: 'reference1')
2. [mandatory] `$channelName` : The channel where the order is from (eg: 'amazon')
3. [mandatory] `$documents` : One or more documents to upload

Example :

```php
namespace ShoppingFeed\Sdk\Api\Order; 

$operation = new OrderOperation();
$operation
    ->uploadDocument('ref1', 'leroymerlin', new Document\Invoice('/tmp/amazon_ref1_invoice.pdf'))
    ->uploadDocument('ref2', 'leroymerlin', new Document\Invoice('/tmp/amazon_ref2_invoice.pdf'));

$orderApi->execute($operation);
```

## Retrieve shipments
To retrieve shipments, you can use these methods :

### getShipmentsByOrder
1. [mandatory] `$orderId` : id (eg: 1234)

Example :

```php
namespace ShoppingFeed\Sdk\Api\Order; 

$shipments = $orderApi->getShipmentsByOrder(1234);
``` 

### getShipments
In the order object :

Example : 

```php
namespace ShoppingFeed\Sdk\Api\Order; 

foreach($orderApi->getAll($criteria['filters']) as $order) {
    $shipments = $order->getShipments();
}
``` 

