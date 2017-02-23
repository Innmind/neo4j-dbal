<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;
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
    ): ClauseInterface {
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
    ): ClauseInterface {
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
    ): ClauseInterface {
        $clause = clone $this;
        $clause->path = $this->path->withProperty($property, $cypher);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function withParameter(string $key, $value): ClauseInterface
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
