<?php
namespace ShoppingFeed\Sdk\Api\Catalog;

use ShoppingFeed\Sdk\Resource\AbstractDomainResource;

class CatalogDomain extends AbstractDomainResource
{
    /**
     * @var string
     */
    protected $resourceClass = CatalogResource::class;

    /**
     * @var null|CatalogResource
     */
    protected $catalog = null;

    public function askForFeedImport($force = null)
    {
        $extra = [];

        if ($force) {
            $extra = [
                'params' => [
                    'skipSecurityChecks' => explode(',', $force),
                ],
            ];
        }

        return $this->askForOperation('importFeed', $extra);
    }

    public function askForClearCache()
    {
        return $this->askForOperation('clearCache');
    }

    protected function askForOperation($operation, $extra = [])
    {
        $catalog = $this->getCatalog();
        if ($catalog) {
            $operationLink = $catalog->getOperationLink();
            if ($operationLink) {
                return $operationLink->post(
                    array_merge(
                        [
                            'operation' => $operation,
                            'catalogId' => $catalog->getId(),
                        ],
                        $extra
                    )
                );
            }
        }

        return null;
    }

    /**
     * @return CatalogResource|null
     */
    public function getCatalog()
    {
        if ($this->catalog === null) {
            $resource = $this->link->get();

            if ($resource) {
                $this->catalog = new CatalogResource($resource, false);
            }
        }

        return $this->catalog;
    }
}
