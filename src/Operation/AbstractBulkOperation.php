<?php
namespace ShoppingFeed\Sdk\Operation;

abstract class AbstractBulkOperation extends AbstractOperation
{
    /**
     * Determine the number of items per request
     *
     * @var int
     */
    private $batchSize = 100;

    /**
     * Determine the number of concurrent requests
     *
     * @var int
     */
    private $poolSize = 10;

    /**
     * @var array
     */
    protected $operations = [];

    /**
     * @param int $batchSize
     *
     * @return $this
     */
    public function setBatchSize($batchSize)
    {
        $this->batchSize = max(1, $batchSize);

        return $this;
    }

    /**
     * @param int $poolSize
     *
     * @return $this
     */
    public function setPoolSize($poolSize)
    {
        $this->poolSize = max(1, $poolSize);

        return $this;
    }

    /**
     * @return int
     */
    public function getBatchSize()
    {
        return $this->batchSize;
    }

    /**
     * @return int
     */
    public function getPoolSize()
    {
        return $this->poolSize;
    }

    public function countOperation($groupedBy = null)
    {
        return count($this->getOperations($groupedBy));
    }

    /**
     * @param callable $callback
     * @param string   $filter Allow to filter operations
     */
    protected function eachBatch(callable $callback, $filter = null)
    {
        foreach (array_chunk($this->getOperations($filter), $this->batchSize) as $chunk) {
            $callback($chunk);
        }
    }

    /**
     * Get operation
     *
     * @param string $filter If operation are grouped get only the group
     *
     * @return array
     */
    protected function getOperations($filter = null)
    {
        if ($filter) {
            return isset($this->operations[$filter]) ? $this->operations[$filter] : [];
        }

        return (array) $this->operations;
    }
}
