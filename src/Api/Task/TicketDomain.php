<?php
namespace ShoppingFeed\Sdk\Api\Task;

use ShoppingFeed\Sdk\Resource\AbstractDomainResource;
use ShoppingFeed\Sdk\Resource\PaginatedResourceCollection;

/**
 * @method TicketResource[] getIterator()
 * @method TicketResource[] getAll($page = 1, $limit = 100)
 * @method TicketResource getOne($identity)
 * @method TicketPaginatedCollection getPage(array $criteria = [])
 */
class TicketDomain extends AbstractDomainResource
{
    /**
     * @var string
     */
    protected $resourceClass = TicketResource::class;

    /**
     * @var string
     */
    protected $paginatedResourcesClass = TicketPaginatedCollection::class;

    /**
     * @param string $reference the resource reference
     *
     * @return null|TicketResource
     */
    public function getByReference($reference)
    {
        $resource = $this->link->get([], ['query' => ['reference' => $reference]]);
        if ($resource && $resource->getProperty('count') > 0) {
            return new TicketResource(
                $resource->getFirstResource('ticket'),
                false
            );
        }

        return null;
    }

    /**
     * @param string $batchId
     *
     * @return null|TicketPaginatedCollection
     */
    public function getByBatch($batchId)
    {
        $resource = $this->link->get([], ['query' => ['batchId' => $batchId]]);
        if ($resource && $resource->getProperty('count') > 0) {
            return new $this->paginatedResourcesClass(
                $resource,
                TicketResource::class
            );
        }

        return null;
    }
}
