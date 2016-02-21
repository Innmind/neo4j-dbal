<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

class CreateClause implements ClauseInterface, PathAwareInterface
{
    use PathAware;

    const IDENTIFIER = 'CREATE';

    private $path;
    private $unique;

    public function __construct(
        Expression\Path $path,
        bool $unique
    ) {
        $this->path = $path;
        $this->unique = $unique;
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return self::IDENTIFIER . ($this->unique ? ' UNIQUE' : '');
    }

    /**
     * {@inheritdoc}
     */
    public function linkedTo(
        string $variable = null,
        array $labels = []
    ): ClauseInterface {
        return new self(
            $this->path->linkedTo($variable, $labels),
            $this->unique
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
            $this->path->through($variable, $type, $direction),
            $this->unique
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
            $this->path->withProperty($property, $cypher),
            $this->unique
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withParameter(string $key, $value): ClauseInterface
    {
        return new self(
            $this->path->withParameter($key, $value),
            $this->unique
        );
    }
}
