<?php

namespace ShoppingFeed\Sdk\Test\Api\Order;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use ShoppingFeed\Sdk;
use ShoppingFeed\Sdk\Api;
use ShoppingFeed\Sdk\Api\Order\Identifier\Id;
use ShoppingFeed\Sdk\Exception\InvalidArgumentException;

class OperationTest extends TestCase
{
    /**
     * @var Api\Order\Operation $instance
     */
    private $instance;

    /**
     * @var int $operationCount
     */
    private $operationCount = 10;

    /**
     * @var Id
     */
    private $identifier;

    public function setUp(): void
    {
        $this->identifier = new Id(123);

        $this->instance = $this
            ->getMockBuilder(Api\Order\Operation::class)
            ->setMethods(['addOperation'])
            ->getMock();
    }

    /**
     * @dataProvider operationDeliverOrRefuseProvider
     */
    public function testDeliverOrRefuseOperation(string $operation, ...$args): void
    {
        $this->instance->expects($this->once())->method('addOperation')->with($this->identifier, ...$args);

        $this->assertInstanceOf(Api\Order\Operation::class, $this->instance->$operation($this->identifier));
    }

    public static function operationDeliverOrRefuseProvider(): array
    {
        return [
            'refuse' => [
                'refuse',
                Api\Order\Operation::TYPE_REFUSE,
            ],
            'deliver' => [
                'deliver',
                Api\Order\Operation::TYPE_DELIVER,
            ],
        ];
    }

    /**
     * @dataProvider operationAcceptOrCancelProvider
     */
    public function testAcceptOperation(string $operation, string $type, array $data): void
    {
        $this->instance
            ->expects($this->once())
            ->method('addOperation')
            ->with($this->identifier, $type, $data);

        $this->assertInstanceOf(
            Api\Order\Operation::class,
            $this->instance->$operation($this->identifier, $data['reason'])
        );
    }

    public static function operationAcceptOrCancelProvider(): array
    {
        return [
            'accept'  => [
                'accept',
                Api\Order\Operation::TYPE_ACCEPT,
                ['reason' => 'noreason'],
            ],
            'cancel' => [
                'cancel',
                Api\Order\Operation::TYPE_CANCEL,
                ['reason' => 'noreason'],
            ],
        ];
    }

    public function testShipOperation(): void
    {
        $this->instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                $this->identifier,
                Api\Order\Operation::TYPE_SHIP,
                [
                    'carrier'        => 'ups',
                    'trackingNumber' => '123654abc',
                    'trackingLink'   => 'http://tracking.lnk',
                    'items'          => [],
                ]
            );

        $this->assertInstanceOf(
            Api\Order\Operation::class,
            $this->instance->ship(
                $this->identifier,
                'ups',
                '123654abc',
                'http://tracking.lnk'
            )
        );
    }

    public function testRefundOperation(): void
    {
        $this->instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                $this->identifier,
                Api\Order\Operation::TYPE_REFUND,
                [
                    'refund' => [
                        'shipping' => true,
                        'products' => [
                            ['reference' => 'item1', 'quantity' => 1],
                            ['reference' => 'item2', 'quantity' => 2],
                        ]
                    ]
                ]
            );

        $this->assertInstanceOf(
            Api\Order\Operation::class,
            $this->instance->refund(
                $this->identifier,
                true,
                [
                    ['reference' => 'item1', 'quantity' => 1],
                    ['reference' => 'item2', 'quantity' => 2],
                ]
            )
        );
    }

    public function testUploadDocumentOperation(): void
    {
        $document = new Api\Order\Document\Invoice('/tmp/ref1_amazon_invoice.pdf');

        $this->instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                $this->identifier,
                Api\Order\Operation::TYPE_UPLOAD_DOCUMENTS,
                ['document' => $document]
            );

        $this->assertInstanceOf(
            Api\Order\Operation::class,
            $this->instance->uploadDocument($this->identifier, $document)
        );
    }

    public function testAcknowledgeOperation(): void
    {
        $data = [
            $this->identifier,
            '123654abc',
            'success',
            'Acknowledged',
        ];

        $this->instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                $this->identifier,
                Api\Order\Operation::TYPE_ACKNOWLEDGE,
                $this->callback(
                    function ($param) {
                        return $param['status'] === 'success'
                            && $param['storeReference'] === '123654abc'
                            && $param['message'] === 'Acknowledged'
                            && !empty($param['acknowledgedAt']);
                    }
                )
            );

        $this->assertInstanceOf(Api\Order\Operation::class, $this->instance->acknowledge(...$data));
    }

    public function testUnacknowledgeOperation(): void
    {
        $data = [
            $this->identifier,
            'Unacknowledged',
        ];

        $this->instance
            ->expects($this->once())
            ->method('addOperation')
            ->with(
                $this->identifier,
                Api\Order\Operation::TYPE_UNACKNOWLEDGE
            );

        $this->assertInstanceOf(Api\Order\Operation::class, $this->instance->unacknowledge(...$data));
    }

    public function testAddOperation(): void
    {
        $orderOperation = new Api\Order\Operation();
        $this->generateOperations($orderOperation);

        $this->assertEquals(
            $this->operationCount,
            $orderOperation->count(Api\Order\Operation::TYPE_ACCEPT)
        );
    }

    private function generateOperations(Api\Order\Operation $orderOperation): void
    {
        for ($i = 0; $i < $this->operationCount; $i++) {
            $orderOperation->addOperation($this->identifier, Api\Order\Operation::TYPE_ACCEPT);
        }
    }

    public function testAddWrongOperation(): void
    {
        $orderOperation = new Api\Order\Operation();

        $this->expectException(InvalidArgumentException::class);

        $orderOperation->addOperation($this->identifier, 'FakeType');
    }

    public function testExecute(): void
    {
        $link = $this->createMock(Sdk\Hal\HalLink::class);
        $link
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn(
                $this->createMock(RequestInterface::class)
            );

        $instance = $this
            ->getMockBuilder(Api\Order\Operation::class)
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

        $this->assertInstanceOf(Api\Order\OrderOperationResult::class, $instance->execute($link));
    }
}
