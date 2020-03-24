<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\PathAware;

use Innmind\Neo4j\DBAL\{
    Clause,
    Query\Parameter,
};
use Innmind\Immutable\Map;

trait PathAware
{
    private Clause\Expression\Path $path;

    public function cypher(): string
    {
        return $this->path->cypher();
    }

    public function linkedTo(string $variable = null, string ...$labels): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->linkedTo($variable, ...$labels);

        return $clause;
    }

    /**
     * {@inheritdoc}
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = Clause\Expression\Relationship::BOTH
    ): Clause {
        $clause = clone $this;
        $clause->path = $this->path->through($variable, $type, $direction);

        return $clause;
    }

    public function withADistanceOf(int $distance): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withADistanceOf($distance);

        return $clause;
    }

    public function withADistanceBetween(int $min, int $max): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withADistanceBetween($min, $max);

        return $clause;
    }

    public function withADistanceOfAtLeast(int $distance): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withADistanceOfAtLeast($distance);

        return $clause;
    }

    public function withADistanceOfAtMost(int $distance): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withADistanceOfAtMost($distance);

        return $clause;
    }

    public function withAnyDistance(): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withAnyDistance();

        return $clause;
    }

    public function withProperty(
        string $property,
        string $cypher
    ): Clause {
        $clause = clone $this;
        $clause->path = $this->path->withProperty($property, $cypher);

        return $clause;
    }

    public function withParameter(string $key, $value): Clause
    {
        $clause = clone $this;
        $clause->path = $this->path->withParameter($key, $value);

        return $clause;
    }

    public function hasParameters(): bool
    {
        return !$this->path->parameters()->empty();
    }

    /**
     * @return Map<string, Parameter>
     */
    public function parameters(): Map
    {
        return $this->path->parameters();
    }
}
