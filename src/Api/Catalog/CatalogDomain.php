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

    /**
     * @param string|null $force List of comma separated values that are used
     * in the `importFeed` operation. Allowed values :
     * - all
     * - products
     * - references
     * - categories
     * Only certain roles are allowed to use the $force option.
     * If the role does not allow it, the option is ignored.
     */
    public function requestFeedImport($force = null)
    {
        $extra = [];

        if ($force) {
            $extra = [
                'params' => [
                    'skipSecurityChecks' => explode(',', $force),
                ],
            ];
        }

        $this->requestOperation('importFeed', $extra);
    }

    public function requestClearCache()
    {
        $this->requestOperation('clearCache');
    }

    protected function requestOperation($operation, $extra = [])
    {
        $catalog = $this->getCatalog();
        if ($catalog) {
            $operationLink = $catalog->getOperationLink();
            if ($operationLink) {
                $operationLink->post(
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
