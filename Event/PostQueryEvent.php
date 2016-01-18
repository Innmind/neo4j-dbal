<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Event;

use Innmind\Neo4j\DBAL\QueryInterface;
use Innmind\Neo4j\DBAL\ResultInterface;

class PostQueryEvent extends PreQueryEvent
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
    public function getResult(): ResultInterface
    {
        return $this->result;
    }
}
