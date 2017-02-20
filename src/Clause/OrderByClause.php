<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

final class OrderByClause implements ClauseInterface
{
    const IDENTIFIER = 'ORDER BY';
    const ASC = 'ASC';
    const DESC = 'DESC';

    private $cypher;
    private $direction;

    public function __construct(string $cypher, string $direction)
    {
        $this->cypher = $cypher;
        $this->direction = $direction;
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return self::IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf(
            '%s %s',
            $this->cypher,
            $this->direction === self::ASC ? 'ASC' : 'DESC'
        );
    }
}
