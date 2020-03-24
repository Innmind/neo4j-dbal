<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Exception\DomainException,
    Exception\LogicException,
};
use Innmind\Immutable\Str;

final class OrderByClause implements Clause
{
    private const IDENTIFIER = 'ORDER BY';
    private const ASC = 'asc';
    private const DESC = 'desc';

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

    /**
     * @param 'asc'|'desc' $direction
     */
    public static function of(string $direction, string $cypher): self
    {
        switch ($direction) {
            case 'asc':
                return self::asc($cypher);

            case 'desc':
                return self::desc($cypher);
        }

        throw new LogicException("Unknown direction '$direction'");
    }

    public static function asc(string $cypher): self
    {
        return new self($cypher, self::ASC);
    }

    public static function desc(string $cypher): self
    {
        return new self($cypher, self::DESC);
    }

    public function identifier(): string
    {
        return self::IDENTIFIER;
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s %s',
            $this->cypher,
            $this->direction === self::ASC ? 'ASC' : 'DESC',
        );
    }
}
