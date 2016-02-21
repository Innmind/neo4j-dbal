<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Event;

use Innmind\Neo4j\DBAL\QueryInterface;
use Symfony\Component\EventDispatcher\Event;

class PreQueryEvent extends Event
{
    private $query;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    /**
     * Return the query about to be executed
     *
     * @return QueryInterface
     */
    public function query(): QueryInterface
    {
        return $this->query;
    }
}
