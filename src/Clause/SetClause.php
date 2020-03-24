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

final class SetClause implements Clause, Parametrable
{
    private const IDENTIFIER = 'SET';

    private string $cypher;
    /** @var Map<string, Parameter> */
    private Map $parameters;

    public function __construct(string $cypher)
    {
        if (Str::of($cypher)->empty()) {
            throw new DomainException;
        }

        $this->cypher = $cypher;
        /** @var Map<string, Parameter> */
        $this->parameters = Map::of('string', Parameter::class);
    }

    public function identifier(): string
    {
        return self::IDENTIFIER;
    }

    public function cypher(): string
    {
        return $this->cypher;
    }

    public function withParameter(string $key, $value): Clause
    {
        if (Str::of($key)->empty()) {
            throw new DomainException;
        }

        $set = new self($this->cypher);
        $set->parameters = ($this->parameters)(
            $key,
            new Parameter($key, $value),
        );

        return $set;
    }

    public function hasParameters(): bool
    {
        return !$this->parameters->empty();
    }

    public function parameters(): Map
    {
        return $this->parameters;
    }
}
