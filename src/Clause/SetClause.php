<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Query\Parameter,
    Exception\InvalidArgumentException
};
use Innmind\Immutable\{
    MapInterface,
    Map
};

final class SetClause implements Clause, Parametrable
{
    const IDENTIFIER = 'SET';

    private $cypher;
    private $parameters;

    public function __construct(string $cypher)
    {
        if (empty($cypher)) {
            throw new InvalidArgumentException;
        }

        $this->cypher = $cypher;
        $this->parameters = new Map('string', Parameter::class);
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
        if (empty($key)) {
            throw new InvalidArgumentException;
        }

        $set = new self($this->cypher);
        $set->parameters = $this->parameters->put(
            $key,
            new Parameter($key, $value)
        );

        return $set;
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
    public function parameters(): MapInterface
    {
        return $this->parameters;
    }
}
