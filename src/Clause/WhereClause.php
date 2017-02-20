<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    ClauseInterface,
    Query\Parameter
};
use Innmind\Immutable\{
    MapInterface,
    Map
};

final class WhereClause implements ClauseInterface, ParametrableInterface
{
    const IDENTIFIER = 'WHERE';

    private $cypher;
    private $parameters;

    public function __construct(string $cypher)
    {
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
    public function withParameter(string $key, $value): ClauseInterface
    {
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
    public function parameters(): MapInterface
    {
        return $this->parameters;
    }
}
