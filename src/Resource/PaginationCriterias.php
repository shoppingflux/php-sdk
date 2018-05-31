<?php
namespace ShoppingFeed\Sdk\Resource;

class PaginationCriterias
{
    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var int
     */
    private $limit = AbstractDomainResource::PER_PAGE;

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @param array $criterias
     */
    public function __construct($criterias)
    {
        $this->page    = $criterias['page'] ?: 1;
        $this->limit   = $criterias['limite'] ?: AbstractDomainResource::PER_PAGE;
        $this->filters = $criterias['filters'] ?: [];
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
     * Convert criterias as ready to be added to URL
     *
     * @return array
     */
    public function toArray()
    {
        $query = array_map(
            'intval',
            [
                'page'  => $this->getPage(),
                'limit' => $this->getLimit(),
            ]
        );

        if ($this->filters) {
            foreach($this->filters as $field => &$values) {
                // The norm in the API for multiple values is to pass them comma separated
                if (is_array($values)) {
                    $values = implode(',', $values);
                }
            }

            $query = array_merge($query, $this->filters);
        }

        return $query;
    }
}
