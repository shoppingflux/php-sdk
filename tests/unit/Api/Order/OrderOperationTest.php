<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use GuzzleHttp\Psr7;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use ShoppingFeed\Sdk;

class OrderOperationTest extends TestCase
{
    private $operationCount = 10;

    /**
     * Generate operations
     *
     * @param Sdk\Api\Order\OrderOperation $orderOperation
     *
     * @throws \Exception
     */
    private function generateOperations(Sdk\Api\Order\OrderOperation $orderOperation)
    {
        for ($i = 0; $i < $this->operationCount; $i++) {
            $orderOperation->addOperation(
                'ref' . $i,
                'amazon',
                Sdk\Api\Order\OrderOperation::TYPE_ACCEPT
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function testAddOperation()
    {
        $orderOperation = new Sdk\Api\Order\OrderOperation();
        $this->generateOperations($orderOperation);

        $this->assertEquals(
            $this->operationCount,
            $orderOperation->count(Sdk\Api\Order\OrderOperation::TYPE_ACCEPT)
        );
    }

    /**
     * @throws \Exception
     */
    public function testAcceptOperation()
    {
        $instance = $this
            ->getMockBuilder(Sdk\Api\Order\OrderOperation::class)
            ->setMethods(['addOperation'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                Sdk\Api\Order\OrderOperation::TYPE_ACCEPT,
                ['reason' => 'noreason']
            );

        $this->assertInstanceOf(
            Sdk\Api\Order\OrderOperation::class,
            $instance->accept(
                'ref1',
                'amazon',
                'noreason'
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testCancelOperation()
    {
        $instance = $this
            ->getMockBuilder(Sdk\Api\Order\OrderOperation::class)
            ->setMethods(['addOperation'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                Sdk\Api\Order\OrderOperation::TYPE_CANCEL,
                ['reason' => 'noreason']
            );

        $this->assertInstanceOf(
            Sdk\Api\Order\OrderOperation::class,
            $instance->cancel(
                'ref1',
                'amazon',
                'noreason'
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testRefuseOperation()
    {
        $instance = $this
            ->getMockBuilder(Sdk\Api\Order\OrderOperation::class)
            ->setMethods(['addOperation'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                Sdk\Api\Order\OrderOperation::TYPE_REFUSE,
                ['refund' => ['item1', 'item2']]
            );

        $this->assertInstanceOf(
            Sdk\Api\Order\OrderOperation::class,
            $instance->refuse(
                'ref1',
                'amazon',
                ['item1', 'item2']
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testShipOperation()
    {
        $instance = $this
            ->getMockBuilder(Sdk\Api\Order\OrderOperation::class)
            ->setMethods(['addOperation'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                Sdk\Api\Order\OrderOperation::TYPE_SHIP,
                [
                    'carrier'        => 'ups',
                    'trackingNumber' => '123654abc',
                    'trackingLink'   => 'http://tracking.lnk',
                ]
            );

        $this->assertInstanceOf(
            Sdk\Api\Order\OrderOperation::class,
            $instance->ship(
                'ref1',
                'amazon',
                'ups',
                '123654abc',
                'http://tracking.lnk'
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testAcknowledgeOperation()
    {
        $data = [
            'ref1',
            'amazon',
            '123654abc',
            'success',
            'Acknowledged',
        ];

        $instance = $this
            ->getMockBuilder(Sdk\Api\Order\OrderOperation::class)
            ->setMethods(['addOperation'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref1',
                'amazon',
                Sdk\Api\Order\OrderOperation::TYPE_ACKNOWLEDGE,
                new \PHPUnit_Framework_Constraint_Callback(
                    function ($param) use ($data) {
                        return $param['status'] === 'success'
                               && $param['storeReference'] === '123654abc'
                               && $param['message'] === 'Acknowledged'
                               && !empty($param['acknowledgedAt']);
                    }
                )
            );

        $this->assertInstanceOf(
            Sdk\Api\Order\OrderOperation::class,
            $instance->acknowledge(...$data)
        );
    }

    /**
     * @throws \Exception
     */
    public function testUnacknowledgeOperation()
    {
        $data = [
            'ref2',
            'amazon2',
            'Unacknowledged',
        ];
        $instance = $this
            ->getMockBuilder(Sdk\Api\Order\OrderOperation::class)
            ->setMethods(['addOperation'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                'ref2',
                'amazon2',
                Sdk\Api\Order\OrderOperation::TYPE_UNACKNOWLEDGE
            );

        $this->assertInstanceOf(
            Sdk\Api\Order\OrderOperation::class,
            $instance->unacknowledge(...$data)
        );
    }

    /**
     * @throws \Exception
     */
    public function testAddWrongOperation()
    {
        $orderOperation = new Sdk\Api\Order\OrderOperation();

        $this->expectException(Sdk\Order\Exception\UnexpectedTypeException::class);

        $orderOperation->addOperation(
            'ref',
            'amazon',
            'FakeType'
        );
    }

    /**
     * @throws \Exception
     */
    public function testExecute()
    {
        $link = $this->createMock(Sdk\Hal\HalLink::class);
        $link
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn(
                $this->createMock(RequestInterface::class)
            );

        /** @var Sdk\Api\Order\OrderOperation|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this
            ->getMockBuilder(Sdk\Api\Order\OrderOperation::class)
            ->setMethods(['getPoolSize'])
            ->getMock();

        $instance
            ->expects($this->once())
            ->method('getPoolSize')
            ->willReturn(10);

        $this->generateOperations($instance);

        $link
            ->expects($this->once())
            ->method('batchSend')
            ->with(
                [$this->createMock(RequestInterface::class)],
                function (Sdk\Hal\HalResource $resource) use (&$resources) {
                    array_push($resources, ...$resource->getResources('order'));
                },
                null,
                [],
                10
            );

        $this->assertInstanceOf(
            Sdk\Api\Task\TicketCollection::class,
            $instance->execute($link)
        );
    }

    public function testAssociationBetweenRefandTicketId()
    {
        $expected = [
            'accept' => [
                'ticket123' => ["abc123", "abc456"],
            ],
        ];

        $instance   = new Sdk\Api\Order\OrderOperation();
        $reflection = new \ReflectionClass(get_class($instance));
        $method     = $reflection->getMethod('associateTicketWithReference');
        $method->setAccessible(true);

        $uri = $this->createMock(Psr7\Uri::class);
        /** @var Sdk\Hal\HalResource|\PHPUnit_Framework_MockObject_MockObject $resource */
        $resource = $this->createMock(Sdk\Hal\HalResource::class);
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request    = $this->createMock(Psr7\Request::class);
        $references = [];

        $resource
            ->expects($this->once())
            ->method('getProperty')
            ->willReturn('id')
            ->willReturn('ticket123');

        $request
            ->expects($this->once())
            ->method('getBody')
            ->willReturn('{"order":[{"reference": "abc123"}, {"reference": "abc456"}]}');

        $uri
            ->expects($this->once())
            ->method('getPath')
            ->willReturn('/fake/accept');

        $request
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $method->invokeArgs($instance, [$resource, $request, &$references]);

        $this->assertEquals($expected, $references);
    }

    public function testCreateRequestGenerator()
    {
        $request  = $this->createMock(RequestInterface::class);
        $link     = $this->createMock(Sdk\Hal\HalLink::class);
        $requests = [];

        $instance = new Sdk\Api\Order\OrderOperation();
        $link
            ->expects($this->once())
            ->method('createRequest')
            ->with('POST', ['operation' => 'type'], ['order' => ['data']])
            ->willReturn($request);

        $reflection = new \ReflectionClass(get_class($instance));
        $method     = $reflection->getMethod('createRequestGenerator');
        $method->setAccessible(true);

        $callable = $method->invokeArgs($instance, ['type', $link, &$requests]);

        $this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_CALLABLE, $callable);

        $callable(['data']);

        $this->assertEquals($request, $requests[0]);
    }

    public function testCreateSuccessBatchsendCallback()
    {
        $request = $this->createMock(RequestInterface::class);
        $request
            ->method('getUri')
            ->willReturn($this->createMock(UriInterface::class));
        $request
            ->method('getBody')
            ->willReturn('{"order":[{"reference":"abc-123"}]}');

        $requests   = [$request];
        $resources  = [];
        $references = [];
        $refIndex   = 0;

        $instance = new Sdk\Api\Order\OrderOperation();

        $reflection = new \ReflectionClass(get_class($instance));
        $method     = $reflection->getMethod('createSuccessBatchsendCallback');
        $resource   = $this->createMock(Sdk\Hal\HalResource::class);
        $method->setAccessible(true);

        $callable = $method->invokeArgs($instance, [&$resources, &$references, &$refIndex, &$requests]);

        $this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_CALLABLE, $callable);

        $callable($resource);

        $this->assertEquals($resource, $resources[0]);
        $this->assertEquals(count($requests), $refIndex);
    }
}
