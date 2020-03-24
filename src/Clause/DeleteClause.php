<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

final class DeleteClause implements Clause
{
    private const IDENTIFIER = 'DELETE';

    private string $cypher;
    private bool $detachable = false;

    public function __construct(string $cypher, bool $detachable)
    {
        if (Str::of($cypher)->empty()) {
            throw new DomainException;
        }

        $this->cypher = $cypher;
        $this->detachable = $detachable;
    }

    public function identifier(): string
    {
        return $this->detachable ?
            'DETACH '.self::IDENTIFIER : self::IDENTIFIER;
    }

    public function cypher(): string
    {
        return $this->cypher;
    }
}
