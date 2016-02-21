<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\Query\Parameter;
use Innmind\Neo4j\DBAL\Exception\LogicException;
use Innmind\Immutable\Collection;
use Innmind\Immutable\TypedCollectionInterface;
use Innmind\Immutable\TypedCollection;

class Path
{
    private $elements;
    private $lastOperation;
    private $parameters;

    private function __construct()
    {
        $this->elements = new Collection([]);
    }

    /**
     * Start the path with the given node
     *
     * @param string $variable
     * @param array $labels
     *
     * @return self
     */
    public static function startWithNode(
        string $variable = null,
        array $labels = []
    ): self {
        $path = new self;
        $path->elements = $path->elements->push(
            new Node($variable, $labels)
        );
        $path->lastOperation = Node::class;

        return $path;
    }

    /**
     * Create a relationship to the given node
     *
     * @param string $variable
     * @param array $labels
     *
     * @return self
     */
    public function linkedTo(string $variable = null, array $labels = []): self
    {
        $path = new self;
        $path->elements = $this
            ->elements
            ->push(new Relationship)
            ->push(new Node($variable, $labels));
        $path->lastOperation = Node::class;

        return $path;
    }

    /**
     * Type the last declared relationship in the path
     *
     * @param string $variable
     * @param string $type
     * @param string $direction
     *
     * @throws LogicException If no relationship in the path
     *
     * @return self
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = Relationship::BOTH
    ): self {
        if ($this->elements->count() < 3) {
            throw new LogicException;
        }

        $node = $this->elements->last();
        $path = new self;
        $path->elements = $this
            ->elements
            ->pop()
            ->pop()
            ->push(new Relationship($variable, $type, $direction))
            ->push($this->elements->last());
        $path->lastOperation = Relationship::class;

        return $path;
    }

    /**
     * Add the given parameter to the last operation
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function withParameter(string $key, $value): self
    {
        if ($this->lastOperation === Node::class) {
            $element = $this->elements->last();
        } else {
            $element = $this->elements->pop()->last();
        }

        $element = $element->withParameter($key, $value);
        $path = new self;
        $path->lastOperation = $this->lastOperation;

        if ($this->lastOperation === Node::class) {
            $path->elements = $this
                ->elements
                ->pop()
                ->push($element);
        } else {
            $path->elements = $this
                ->elements
                ->pop()
                ->pop()
                ->push($element)
                ->push($this->elements->last());
        }

        return $path;
    }

    /**
     * Add the given property to the last operation
     *
     * @param string $property
     * @param string $cypher
     *
     * @return self
     */
    public function withProperty(string $property, string $cypher): self
    {
        if ($this->lastOperation === Node::class) {
            $element = $this->elements->last();
        } else {
            $element = $this->elements->pop()->last();
        }

        $element = $element->withProperty($property, $cypher);
        $path = new self;
        $path->lastOperation = $this->lastOperation;

        if ($this->lastOperation === Node::class) {
            $path->elements = $this
                ->elements
                ->pop()
                ->push($element);
        } else {
            $path->elements = $this
                ->elements
                ->pop()
                ->pop()
                ->push($element)
                ->push($this->elements->last());
        }

        return $path;
    }

    /**
     * Return all the parameters of the path
     *
     * @return TypedCollectionInterface
     */
    public function parameters(): TypedCollectionInterface
    {
        if ($this->parameters) {
            return $this->parameters;
        }

        $this->parameters = new TypedCollection(
            Parameter::class,
            []
        );

        $this->elements->each(function ($index, $element) {
            $this->parameters = $this->parameters->merge($element->parameters());
        });

        return $this->parameters;
    }

    public function __toString(): string
    {
        return $this->elements->join('');
    }
}
