<?php
namespace ShoppingFeed\Sdk\Api\Credential;

use Jsor\HalClient\HalClientInterface;

class Password implements CredentialInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = trim($username);
        $this->password = trim($password);
    }

    /**
     * @inheritdoc
     */
    public function authenticate(HalClientInterface $client)
    {
        $response = $client->post('v1/auth', [
            'body' => [
                'grant_type' => 'password',
                'username'   => $this->username,
                'password'   => $this->password,
            ],
        ]);

        return (new Token($response->getProperty('access_token')))->authenticate($client);
    }
}
