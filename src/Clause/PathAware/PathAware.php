<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\PathAware;

use Innmind\Neo4j\DBAL\Clause;
use Innmind\Immutable\MapInterface;

trait PathAware
{
    private $path;

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return (string) $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function linkedTo(
        string $variable = null,
        array $labels = []
    ): Clause {
        $clause = clone $this;
        $clause->path = $this->path->linkedTo($variable, $labels);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = Expression\Relationship::BOTH
    ): Clause {
        $clause = clone $this;
        $clause->path = $this->path->through($variable, $type, $direction);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function withProperty(
        string $property,
        string $cypher
    ): Clause {
        $clause = clone $this;
        $clause->path = $this->path->withProperty($property, $cypher);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function withParameter(string $key, $value): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withParameter($key, $value);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameters(): bool
    {
        return $this->path->parameters()->size() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function parameters(): MapInterface
    {
        return $this->path->parameters();
    }
}
