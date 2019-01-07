<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Exception\DomainException,
};

final class OnCreateClause implements Clause
{
    const IDENTIFIER = 'ON CREATE';

    private $cypher;

    public function __construct(string $cypher)
    {
        if (empty($cypher)) {
            throw new DomainException;
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
