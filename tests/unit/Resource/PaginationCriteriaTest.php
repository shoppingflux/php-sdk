<?php
namespace ShoppingFeed\Sdk\Test\Resource;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk\Resource\PaginationCriteria;

class PaginationCriteriaTest extends TestCase
{
    private $data = [
        'page'    => 12,
        'limit'   => 12,
        'filters' => [
            'field1' => 'value1',
            'field2' => 2,
            'field3' => ['value3', 'value4'],
        ],
    ];

    public function testGetters()
    {
        $instance = new PaginationCriteria($this->data);

        $this->assertEquals($this->data['page'], $instance->getPage());
        $this->assertEquals($this->data['limit'], $instance->getLimit());
        $this->assertEquals($this->data['filters'], $instance->getFilters());
    }

    public function testGetQueryParams()
    {
        $instance  = new PaginationCriteria($this->data);
        $extracted = $instance->getQueryParams();

        $this->assertEquals($this->data['page'], $extracted['page']);
        $this->assertEquals($this->data['limit'], $extracted['limit']);
        foreach ($this->data['filters'] as $field => $value) {
            if (is_array($value)) {
                $this->assertEquals(implode(',', $value), $extracted[$field]);
                continue;
            }

            $this->assertEquals($value, $extracted[$field]);
        }
    }
}
