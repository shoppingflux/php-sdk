<?php
namespace ShoppingFeed\Sdk\Operation;

use Crell\ApiProblem\ApiProblem;
use Jsor\HalClient\Exception\BadResponseException;

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
    private $poolSize = 1;

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
     * @param callable $callback
     */
    protected function eachBatch(callable $callback)
    {
        foreach (array_chunk($this->operations, $this->batchSize) as $chunk) {
            $callback($chunk);
        }
    }
}
