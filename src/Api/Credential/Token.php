<?php
namespace ShoppingFeed\Sdk\Api\Credential;

use Jsor\HalClient\HalClientInterface;
use ShoppingFeed\Sdk\Session\SessionResource;

class Token implements CredentialInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = trim($token);
    }

    /**
     * @inheritdoc
     */
    public function authenticate(HalClientInterface $client)
    {
        $client = $client->withHeader('Authorization', 'Bearer ' . $this->token);

        return new SessionResource(
            $client->get('v1/me'),
            false
        );
    }
}
