<?php

namespace ShoppingFeed\Sdk\Credential;

use ShoppingFeed\Sdk\Hal;

interface CredentialInterface
{
    /**
     *
     * @return \ShoppingFeed\Sdk\Api\Session\SessionResource
     */
    public function authenticate(Hal\HalClient $client);
}
