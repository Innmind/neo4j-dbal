<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Query\Parameter,
    Exception\DomainException,
};
use Innmind\Immutable\{
    Map,
    Str,
};

final class WhereClause implements Clause, Parametrable
{
    private const IDENTIFIER = 'WHERE';

    private string $cypher;
    private Map $parameters;

    public function __construct(string $cypher)
    {
        if (Str::of($cypher)->empty()) {
            throw new DomainException;
        }

        $this->cypher = $cypher;
        $this->parameters = Map::of('string', Parameter::class);
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

    /**
     * {@inheritdoc}
     */
    public function withParameter(string $key, $value): Clause
    {
        if (Str::of($key)->empty()) {
            throw new DomainException;
        }

        $where = new self($this->cypher);
        $where->parameters = $this->parameters->put(
            $key,
            new Parameter($key, $value)
        );

        return $where;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameters(): bool
    {
        return $this->parameters->size() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function parameters(): Map
    {
        return $this->parameters;
    }
}
