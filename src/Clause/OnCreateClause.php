<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

final class OnCreateClause implements Clause
{
    private const IDENTIFIER = 'ON CREATE';

    private string $cypher;

    public function __construct(string $cypher)
    {
        if (Str::of($cypher)->empty()) {
            throw new DomainException;
        }

        $this->cypher = $cypher;
    }

    public function identifier(): string
    {
        return self::IDENTIFIER;
    }

    public function cypher(): string
    {
        return $this->cypher;
    }
}
