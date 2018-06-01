<?php
namespace ShoppingFeed\Sdk\Resource;

class Paginationcriteria
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var array
     */
    private $filters;

    /**
     * @param array $criteria
     */
    public function __construct($criteria = [])
    {
        $this->page    = (int) ($criteria['page'] ?: 1);
        $this->limit   = (int) ($criteria['limit'] ?: AbstractDomainResource::PER_PAGE);
        $this->filters = (array) ($criteria['filters'] ?: []);
    }

    /**
     * Current page to get
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Item per page to retrieve
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Filters to apply to query
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }


    /**
     * Convert criteria as ready to be added to URL
     *
     * @return array
     */
    public function toArray()
    {
        $query = [
            'page'  => $this->page,
            'limit' => $this->limit,
        ];

        if ($this->filters) {
            foreach ($this->filters as $field => $values) {
                // The norm in the API for multiple values is to pass them comma separated
                if (is_array($values)) {
                    $query[$field] = implode(',', $values);
                    continue;
                }

                $query[$field] = (string) $values;
            }
        }

        return $query;
    }
}
