<?php
namespace ShoppingFeed\Sdk\Test\Api\Task;

use ShoppingFeed\Sdk\Api\Task\TicketCollection;
use ShoppingFeed\Sdk\Test\Api\AbstractResourceTest;

class TicketCollectionTest extends AbstractResourceTest
{
    public function setUp()
    {
        $this->props = [
            'id' => '123abc',
        ];
    }

    public function testGetproperty()
    {
        $this->initHalResourceProperties();

        $instance = new TicketCollection($this->halResource);

        $this->assertEquals($this->props['id'], $instance->getId());
    }

    public function testIsProcessing()
    {

    }
}
