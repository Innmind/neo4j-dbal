<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

final class OrderByClause implements Clause
{
    private const IDENTIFIER = 'ORDER BY';
    private const ASC = 'ASC';
    private const DESC = 'DESC';

    private string $cypher;
    private string $direction;

    private function __construct(string $cypher, string $direction)
    {
        if (Str::of($cypher)->empty()) {
            throw new DomainException;
        }

        $this->cypher = $cypher;
        $this->direction = $direction;
    }

    public static function asc(string $cypher): self
    {
        return new self($cypher, self::ASC);
    }

    public static function desc(string $cypher): self
    {
        return new self($cypher, self::DESC);
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
        return \sprintf(
            '%s %s',
            $this->cypher,
            $this->direction === self::ASC ? 'ASC' : 'DESC'
        );
    }
}
