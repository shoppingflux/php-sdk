<?php
namespace ShoppingFeed\Sdk\Core\Operation;

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
     * @param          $result
     *
     * @return mixed
     */
    protected function chunk(callable $callback, $result)
    {
        $errors = [];
        foreach (array_chunk($this->operations, $this->batchSize) as $chunk) {
            try {
                $callback($chunk, $result);
            } catch (BadResponseException $exception) {
                // instantiate API problem
                $errors[] = ApiProblem::fromJson((string) $exception->getResponse()->getBody());
            }
        }

        return new BulkOperationResult($result, $errors);
    }
}
