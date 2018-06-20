<?php
namespace ShoppingFeed\Sdk\Api\Session;

use ShoppingFeed\Sdk\Resource\AbstractResource;
use ShoppingFeed\Sdk\Api\Store;

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
     * @return string
     */
    public function getToken()
    {
        return $this->resource->getProperty('token');
    }

    /**
     * @return Store\StoreCollection
     */
    public function getStores()
    {
        return new Store\StoreCollection(
            $this->resource->getResources('store')
        );
    }

    /**
     * @param int|string $idOrName
     *
     * @return \ShoppingFeed\Sdk\Api\Store\StoreResource
     */
    public function selectStore($idOrName)
    {
        $stores = $this->getStores();

        if (is_int($idOrName) || ctype_digit($idOrName)) {
            return $stores->getById($idOrName);
        }

        return $stores->getByName($idOrName);
    }

    /**
     * @return \ShoppingFeed\Sdk\Api\Store\StoreResource|null
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
