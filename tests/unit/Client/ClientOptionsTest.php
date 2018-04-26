<?php
namespace ShoppingFeed\Sdk\Test\Client;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ShoppingFeed\Sdk\Client\ClientOptions;

class ClientOptionsTest extends TestCase
{
    public function testGetterSetter()
    {
        $uri      = 'http://uri.com';
        $logger   = $this->createMock(LoggerInterface::class);
        $instance = new ClientOptions();
        $instance
            ->setLogger($logger)
            ->setRetryOnServerError(5)
            ->setHandleRateLimit(true)
            ->setBaseUri($uri);

        $this->assertSame($logger, $instance->getLogger());
        $this->assertEquals(5, $instance->getRetryOnServerError());
        $this->assertEquals($uri, $instance->getBaseUri());
        $this->assertTrue($instance->handleRateLimit());
    }
}
