<?php

namespace ShoppingFeed\Sdk\Api\Credential;

use Jsor\HalClient\HalClientInterface;

interface CredentialInterface
{
    /**
     * @param HalClientInterface $client
     *
     * @return \ShoppingFeed\Sdk\Core\Session\SessionResource
     */
    public function authenticate(HalClientInterface $client);
}