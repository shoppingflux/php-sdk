# Ticket

We handle most task asynchronously and to follow all tasks we have created a system of ticketing.  
A ticket represent a task in our system and will contain the task timing and a current state.

## Access

To get a ticket detail you need to call ticket API like so :
```php
<?php
$ticketApi = $session->getMainStore()->getTicketApi();

/** @var \ShoppingFeed\Sdk\Api\Task\TicketResource $ticket */
$ticket = $ticketApi->getByReference('abc123def456ghi789');

$ticket->getStatus();
```

## Batch

When an task is requested we create a batch, a batch is a list of ticket of all operation needed for 
this task.  
A batch ID is then returned to the user to be able to retrieve all tickets needed to handle the requested task.
```php
<?php
$ticketApi = $session->getMainStore()->getTicketApi();

/** @var \ShoppingFeed\Sdk\Api\Task\PaginatedTicketCollection $batch */
$ticketCollection = $ticketDomain->getByBatch('987ihg654fed321cba');

// Global status of the batch based on all ticket status
$ticketCollection->isBeingProcessed();

foreach ($ticketCollection as $ticket) {
    $ticket->getStatus();
}
```
