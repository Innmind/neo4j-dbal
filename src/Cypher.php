<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Exception\InvalidArgumentException
};
use Innmind\Immutable\{
    MapInterface,
    Map
};

final class Cypher implements QueryInterface
{
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
    public function cypher(): string
    {
        return $this->cypher;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->cypher();
    }

    /**
     * {@inheritdoc}
     */
    public function parameters(): MapInterface
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameters(): bool
    {
        return $this->parameters->size() > 0;
    }

    /**
     * Attach parameters to this query
     *
     * @param array $parameters
     *
     * @throws NonParametrableClauseException
     *
     * @return self
     */
    public function withParameters(array $parameters): self
    {
        $query = $this;

        foreach ($parameters as $key => $parameter) {
            $query = $query->withParameter($key, $parameter);
        }

        return $query;
    }

    /**
     * Attach the given parameter to this query
     *
     * @param string $key
     * @param mixed $parameter
     *
     * @throws NonParametrableClauseException
     *
     * @return self
     */
    public function withParameter(string $key, $parameter): self
    {
        if (empty($key)) {
            throw new InvalidArgumentException;
        }

        $query = new self($this->cypher);
        $query->parameters = $this->parameters->put(
            $key,
            new Parameter($key, $parameter)
        );

        return $query;
    }
}
