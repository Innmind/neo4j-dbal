<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Exception\InvalidArgumentException
};

final class OrderByClause implements Clause
{
    const IDENTIFIER = 'ORDER BY';
    const ASC = 'ASC';
    const DESC = 'DESC';

    private $cypher;
    private $direction;

    public function __construct(string $cypher, string $direction)
    {
        if (
            empty($cypher) ||
            !in_array($direction, [self::ASC, self::DESC], true)
        ) {
            throw new InvalidArgumentException;
        }

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
