<?php
namespace ShoppingFeed\Sdk\Core\Session;

use ShoppingFeed\Sdk\Core\Resource\AbstractResource;
use ShoppingFeed\Sdk\Core\Store;

class SessionResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->resource->getProperty('login');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->resource->getProperty('email');
    }

    /**
     * @return Store\StoreCollection
     */
    public function getStores()
    {
        return new Store\StoreCollection(
            $this->resource->getResource('store')
        );
    }

    /**
     * @param int|string $idOrName
     *
     * @return Store\StoreResource
     */
    public function selectStore($idOrName)
    {
        $stores = $this->getStores();

        if (ctype_digit($idOrName)) {
            return $stores->getById($idOrName);
        }

        return $stores->getByName($idOrName);
    }

    /**
     * @return Store\StoreResource|null
     */
    public function getMainStore()
    {
        $resource = $this->resource->getFirstResource('store');
        if ($resource) {
            return new Store\StoreResource($resource, true);
        }

        return null;
    }
}
