<?php
namespace ShoppingFeed\Sdk\Test\Operation;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Operation\AbstractBulkOperation;

class AbstractBulkOperationTest extends TestCase
{
    public function testGetterSetters()
    {
        /** @var AbstractBulkOperation $instance */
        $instance = $this->getMockForAbstractClass(AbstractBulkOperation::class);

        $instance
            ->setBatchSize(10)
            ->setPoolSize(20);

        $this->assertEquals(10, $instance->getBatchSize());
        $this->assertEquals(20, $instance->getPoolSize());
    }

    public function testEachBatchCallback()
    {
        $operations = [];
        for ($i = 0; $i < 50; $i++) {
            $operations[] = 'operation' . $i;
        };
        $instance = new BulkOperationMock($operations);
        $instance->setBatchSize(2);

        $count    = 0;
        $opeCount = count($operations);
        $tester   = $this;
        $instance->eachBatch(
            function ($chunck) use ($tester, $opeCount, &$count) {
                if ($opeCount % 2 && $opeCount - $count === 1) {
                    $tester->assertCount(1, $chunck);
                } else {
                    $tester->assertCount(2, $chunck);
                    $count += 2;
                }
            }
        );
    }

    public function testCountOperation()
    {
        $countOperation = 50;
        $operations     = [];
        for ($i = 0; $i < $countOperation; $i++) {
            $operations[] = 'operation' . $i;
        };
        $instance = new BulkOperationMock($operations);

        $this->assertEquals($countOperation, $instance->countOperation());
    }
}
