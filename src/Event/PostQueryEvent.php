<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Event;

use Innmind\Neo4j\DBAL\{
    QueryInterface,
    ResultInterface
};

final class PostQueryEvent extends PreQueryEvent
{
    private $result;

    public function __construct(QueryInterface $query, ResultInterface $result)
    {
        parent::__construct($query);
        $this->result = $result;
    }

    /**
     * Return the query result
     *
     * @return ResultInterface
     */
    public function result(): ResultInterface
    {
        return $this->result;
    }
}
