<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    ClauseInterface,
    Exception\InvalidArgumentException
};

final class ForeachClause implements ClauseInterface
{
    const IDENTIFIER = 'FOREACH';

    private $cypher;

    public function __construct(string $cypher)
    {
        if (empty($cypher)) {
            throw new InvalidArgumentException;
        }

        $this->cypher = $cypher;
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
        return $this->cypher;
    }
}
