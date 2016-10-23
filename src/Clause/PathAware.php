<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Immutable\TypedCollectionInterface;

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
        return new self(
            $this->path->linkedTo($variable, $labels)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = Expression\Relationship::BOTH
    ): ClauseInterface {
        return new self(
            $this->path->through($variable, $type, $direction)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withProperty(
        string $property,
        string $cypher
    ): ClauseInterface {
        return new self(
            $this->path->withProperty($property, $cypher)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withParameter(string $key, $value): ClauseInterface
    {
        return new self(
            $this->path->withParameter($key, $value)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameters(): bool
    {
        return $this->path->parameters()->count() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function parameters(): TypedCollectionInterface
    {
        return $this->path->parameters();
    }
}
