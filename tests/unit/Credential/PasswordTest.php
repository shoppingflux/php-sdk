<?php
namespace ShoppingFeed\Sdk\Test\Credential;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk;

class PasswordTest extends TestCase
{
    public function testAuthenticate()
    {
        $token    = $this->createMock(Sdk\Credential\Token::class);
        $response = $this->createMock(Sdk\Hal\HalResource::class);
        $client   = $this->createMock(Sdk\Hal\HalClient::class);
        $client
            ->expects($this->once())
            ->method('request')
            ->with('POST', 'v1/account/login', [
                'json' => [
                    'grant_type' => 'password',
                    'username'   => 'username',
                    'password'   => 'password',
                ],
            ])
            ->willReturn($response);

        $instance = $this
            ->getMockBuilder(Sdk\Credential\Password::class)
            ->setConstructorArgs(['username', 'password'])
            ->setMethods(['tokenizeResponse'])
            ->getMock();

        $token
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn($this->createMock(Sdk\Api\Session\SessionResource::class));

        $instance
            ->expects($this->once())
            ->method('tokenizeResponse')
            ->willReturn($token);

        $instance->authenticate($client);
    }

    public function testTokenizeResponse()
    {
        $response = $this->createMock(Sdk\Hal\HalResource::class);
        $response
            ->expects($this->once())
            ->method('getProperty')
            ->with('access_token')
            ->willReturn('12345789');

        $instance = new Sdk\Credential\Password('username', 'password');
        $token = $instance->tokenizeResponse($response);

        $this->assertInstanceOf(Sdk\Credential\Token::class, $token);
    }
}
