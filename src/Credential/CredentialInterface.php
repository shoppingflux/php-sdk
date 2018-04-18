<?php

namespace ShoppingFeed\Sdk\Credential;

use Jsor\HalClient\HalClientInterface;

interface CredentialInterface
{
    /**
     * @param HalClientInterface $client
     *
     * @return \ShoppingFeed\Sdk\Session\SessionResource
     */
    public function authenticate(HalClientInterface $client);
}
