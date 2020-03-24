<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\{
    Query as QueryInterface,
    Exception\DomainException,
};
use Innmind\Immutable\{
    Map,
    Str,
};

final class Cypher implements QueryInterface
{
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

    public function cypher(): string
    {
        return $this->cypher;
    }

    public function parameters(): Map
    {
        return $this->parameters;
    }

    public function hasParameters(): bool
    {
        return !$this->parameters->empty();
    }

    /**
     * Attach parameters to this query
     *
     * @param array<string, mixed> $parameters
     */
    public function withParameters(array $parameters): self
    {
        $query = $this;

        /** @var mixed $parameter */
        foreach ($parameters as $key => $parameter) {
            $query = $query->withParameter($key, $parameter);
        }

        return $query;
    }

    /**
     * Attach the given parameter to this query
     *
     * @param mixed $parameter
     */
    public function withParameter(string $key, $parameter): self
    {
        if (Str::of($key)->empty()) {
            throw new DomainException;
        }

        $query = new self($this->cypher);
        $query->parameters = ($this->parameters)(
            $key,
            new Parameter($key, $parameter),
        );

        return $query;
    }
}
