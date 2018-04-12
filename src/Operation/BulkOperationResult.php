<?php
namespace ShoppingFeed\Sdk\Operation;

use Crell\ApiProblem\ApiProblem;
use ShoppingFeed\Sdk\Resource\AbstractCollection;

class BulkOperationResult
{
    /**
     * @var ApiProblem[]
     */
    private $errors = [];

    /**
     * @var AbstractCollection
     */
    private $resource;

    /**
     * @param AbstractCollection $collection
     * @param ApiProblem[]       $errors
     */
    public function __construct(AbstractCollection $collection, array $errors = [])
    {
        $this->resource = $collection;
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    /**
     * @param ApiProblem $problem
     */
    private function addError(ApiProblem $problem)
    {
        $this->errors[] = $problem;
    }

    /**
     * @return null|AbstractCollection
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return (bool) $this->errors;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->errors && ! count($this->resource);
    }

    /**
     * @return ApiProblem[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
