<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\PathAware;

use Innmind\Neo4j\DBAL\Clause;
use Innmind\Immutable\MapInterface;

trait PathAware
{
    private Clause\Expression\Path $path;

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
    public function withADistanceOf(int $distance): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withADistanceOf($distance);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function withADistanceBetween(int $min, int $max): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withADistanceBetween($min, $max);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function withADistanceOfAtLeast(int $distance): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withADistanceOfAtLeast($distance);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function withADistanceOfAtMost(int $distance): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withADistanceOfAtMost($distance);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function withAnyDistance(): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withAnyDistance();

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
