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
     * @param string   $groupedBy Allow to group operations
     */
    protected function eachBatch(callable $callback, $groupedBy = null)
    {
        foreach (array_chunk($this->getOperations($groupedBy), $this->batchSize) as $chunk) {
            $callback($chunk);
        }
    }

    /**
     * Get operation
     *
     * @param string $groupedBy If operation are grouped get only the group
     *
     * @return array
     */
    protected function getOperations($groupedBy = null)
    {
        if ($groupedBy) {
            return isset($this->operations[$groupedBy]) ? $this->operations[$groupedBy] : [];
        }

        return (array) $this->operations;
    }
}
