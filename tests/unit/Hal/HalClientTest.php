<?php
namespace ShoppingFeed\Sdk\Test\Hal;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ShoppingFeed\Sdk\Hal\HalClient;
use ShoppingFeed\Sdk\Hal\HalResource;

class HalClientTest extends TestCase
{
    public function testCreateRequest()
    {
        $instance = new HalClient('http://fake.uri');
        $request  = $instance->createRequest('GET', '/test', ['FakeHeader' => 'HeaderValue'], 'test');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals('test', $request->getBody());
        $this->assertEquals('HeaderValue', $request->getHeaderLine('FakeHeader'));
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/test', $request->getUri());
    }

    public function testRequest()
    {
        /** @var HalClient|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(HalClient::class)
            ->setConstructorArgs(['http://fake.uri'])
            ->setMethods(['send', 'createRequest'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('send')
            ->willReturn(true);
        $instance
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn($this->createMock(Request::class));

        $this->assertTrue($instance->request('GET', '/test'));
    }

    public function testCreateResource()
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn('{"foo":"bar", "foo2":"bar2"}');

        $instance = new HalClient('http://fake.uri');
        $resource = $instance->createResource($response);

        $this->assertInstanceOf(HalResource::class, $resource);
    }

    public function testWithToken()
    {
        $instance = new HalClient('http://fake.uri');
        $client   = $instance->withToken('3213213132132131');

        $this->assertInstanceOf(HalClient::class, $client);
        $this->assertNotSame($instance, $client);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSend()
    {
        $clientInstanceMock = $this
            ->getMockBuilder(HalClient::class)
            ->setConstructorArgs(['http://fake.uri'])
            ->setMethods(['createResource'])
            ->getMock();
        $clientInstance = new HalClient('http://fake.uri');
        $reflection     = new \ReflectionClass($clientInstance);
        $client         = $this->createMock(Client::class);

        $client
            ->expects($this->once())
            ->method('send')
            ->willReturn(
                $this->createMock(ResponseInterface::class)
            );

        $clientProp = $reflection->getProperty('client');
        $clientProp->setAccessible(true);
        $clientProp->setValue($clientInstanceMock, $client);

        $clientInstanceMock
            ->expects($this->once())
            ->method('createResource')
            ->willReturn($this->createMock(HalResource::class));

        $clientInstanceMock->send($this->createMock(RequestInterface::class));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     */
    public function testSendNoResponse()
    {
        $clientInstance = new HalClient('http://fake.uri');
        $reflection     = new \ReflectionClass($clientInstance);
        $client         = $this->createMock(Client::class);

        $client
            ->expects($this->once())
            ->method('send')
            ->willReturn(null);

        $clientProp = $reflection->getProperty('client');
        $clientProp->setAccessible(true);
        $clientProp->setValue($clientInstance, $client);

        $result = $clientInstance->send($this->createMock(RequestInterface::class));
        $this->assertNull($result);
    }
}
