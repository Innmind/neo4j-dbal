<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Immutable\TypedCollectionInterface;

class MatchClause implements ClauseInterface, ParametrableInterface
{
    const IDENTIFIER = 'MATCH';

    private $path;

    public function __construct(Expression\Path $path)
    {
        $this->path = $path;
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
        return (string) $this->path;
    }

    /**
     * Link the currently matched node to another node
     *
     * @param string $variable
     * @param array $labels
     *
     * @return self
     */
    public function linkedTo(string $variable = null, array $labels = []): self
    {
        return new self(
            $this->path->linkedTo($variable, $labels)
        );
    }

    /**
     * Type the last connection
     *
     * @param string $variable
     * @param string $type
     * @param string $direction
     *
     * @return self
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = Expression\Relationship::BOTH
    ): self {
        return new self(
            $this->path->through($variable, $type, $direction)
        );
    }

    /**
     * Specify a property to be matched
     *
     * @param string $property
     * @param string $cypher
     *
     * @return self
     */
    public function withProperty(string $property, string $cypher): self
    {
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
