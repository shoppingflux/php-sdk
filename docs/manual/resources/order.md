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

### Deprecated

1. Usage of getMainStore() has been deprecated and SHOULD NOT be used anymore. 
It can lead to major issue if your session get access to new stores.
It will be removed in the next major version of the SDK.

```php
$storeId  = 1276;
$orderApi = $session->selectStore($storeId)->getOrderApi();
```

2. Usage of reference & channelName as identifier of orders has been deprecated
   and SHOULD NOT be used anymore.

Please use :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\Operation();
$operation->accept(new Id(111));
$orderApi->execute($operation);
```

Instead of :

- `$reference` : Order reference (eg: 'reference1')
- `$channelName` : The channel where the order is from (eg: 'amazon')

```
// Deprecated version
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation->accept('ref1', 'amazon')
$orderApi->execute($operation);
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
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new \ShoppingFeed\Sdk\Api\Order\Operation();
$operation
    ->accept(new Id(111))
    ->refuse(new Id(222))
    ->ship(new Id(333))
    ->cancel(new Id(444));

$orderApi->execute($operation);
```

Operations allowed on existing orders will always be accepted, as they are treated asynchronously.    
When sending operations on an order, you will receive batch IDs. 
See [ticket documentation](ticket.md) for more information on how to retrieve ticket and batch information.  

```php
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->accept(new Id(111))
    ->refuse(new Id(222))
    ->ship(new Id(333))
    ->cancel(new Id(444))
    ->refund(new Id(555));

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

The accept operation accepts 2 parameters :
1. [mandatory] `$orderId` : Order ID (eg: 1234)
2. [optional] `$reason` : The reason of the acceptance (eq: 'Why we accept the order') 

Example :

```php
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new \ShoppingFeed\Sdk\Api\Order\Operation();
$operation
    ->accept(new Id(1234))
    ->accept(new Id(5678), 'Why we accept it');

$orderApi->execute($operation);
```

### Cancel

The cancel operation accepts 2 parameters :
1. [mandatory] `$orderId` : Order ID (eg: 1234)
2. [optional] `$reason` : The reason of the cancelling (eq: 'Why we cancel the order')

Example :

```php
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->cancel(new Id(1234))
    ->cancel(new Id(5678), 'Why we accept it');

$orderApi->execute($operation);
```

### Refuse

The refuse operation accepts 1 parameter :
1. [mandatory] `$orderId` : Order ID (eg: 1234)

Example :

```php
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->refuse(new Id(1234));

$orderApi->execute($operation);
```

### Ship

The ship operation accepts 5 parameters :

1. [mandatory] `$orderId` : Order ID (eg: 1234)
2. [optional] `$carrier` : The carrier name used for the shipment (eq: 'ups') 
3. [optional] `$trackingNumber` : Tracking number (eq: '01234598abcdef') 
4. [optional] `$trackingLink` : Tracking link (eq: 'http://tracking.url/') 
5. [optional] `$items` : Array of order item id and quantity to be shipped (eq: [['id' => 1234, 'quantity' => 1]]) 

Example :

```php
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->ship(new Id(1234))
    ->ship(new Id(5678), 'ups', '123456789abcdefg', 'http://tracking.url/');

$orderApi->execute($operation);
```

### Refund

The refund operation accepts 3 parameters :
1. [mandatory] `$orderId` : Order ID (eg: 1234)
2. [optional] `$shipping` : true if shipping cost need to be refunded, false otherwise (eq: `false`) 
3. [optional] `$products` : Item references and their quantities to refund (eq: `['itemref1' => 1, 'itemref2' => 2]`) 

Example :

```php
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

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
    ->refund(new Id(1234))
    ->refund(new Id(5678), true, $products);

$orderApi->execute($operation);
```

### Acknowledge

To acknowledge the reception of an order, you need the following parameters :

1. [mandatory] `$orderId` : Order ID (eg: 1234)
2. [optional] `$storeReference` : Store reference (eg: 'store-reference')
3. [optional] `$status` : Status of acknowledgment (eg: 'success' or 'error')
4. [optional] `$message` : In case or error status, you can provide a message (eg: 'Unknown product #ABC-123') 

Example :

```php
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->acknowledge(new Id(1234), 'store-reference', 'success')
    ->acknowledge(new Id(5678), 'store-reference', 'error')
    ->acknowledge(new Id(9012), 'store-reference', 'error', 'Order well acknowledged');

$orderApi->execute($operation);
```

### Unacknowledge

To unacknowledge the reception of an order previously acknowledged, you need the following parameters :

1. [mandatory] `$orderId` : Order ID (eg: 1234)

Example :

```php
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation
    ->unacknowledge(new Id(1111))
    ->unacknowledge(new Id(2222));
    ->unacknowledge(new Id(3333));

$orderApi->execute($operation);
```

### Upload documents

To upload order documents, you need the following parameters :
1. [mandatory] `$orderId` : Order ID (eg: 1234)
2. [mandatory] `$documents` : One or more documents to upload

Example :

```php
namespace ShoppingFeed\Sdk\Api\Order; 

use ShoppingFeed\Sdk\Api\Order\Identifier\Id;

$operation = new OrderOperation();
$operation
    ->uploadDocument(new Id(1111), new Document\Invoice('/tmp/amazon_ref1_invoice.pdf'))
    ->uploadDocument(new Id(2222), new Document\Invoice('/tmp/amazon_ref2_invoice.pdf'));

$orderApi->execute($operation);
```

### Deliver

The deliver operation accepts 1 parameter:

1. [mandatory] `$orderId` : Order ID (eg: 1234)

Example :

```php
$operation = new \ShoppingFeed\Sdk\Api\Order\OrderOperation();
$operation->deliver(new Id(1111));
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

