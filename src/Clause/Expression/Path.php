<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Exception\LogicException,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
    Stream,
};

final class Path
{
    private $elements;
    private $lastOperation;
    private $parameters;

    private function __construct()
    {
        $this->elements = new Stream('object');
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
        $path->elements = $path->elements->add(
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
            ->add(new Relationship)
            ->add(new Node($variable, $labels));
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
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = new self;
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add(new Relationship($variable, $type, $direction))
            ->add($this->elements->last());
        $path->lastOperation = Relationship::class;

        return $path;
    }

    /**
     * Define the deepness of the relationship
     *
     * @param int $distance
     *
     * @throws LogicException If no relationship in the path
     *
     * @return self
     */
    public function withADistanceOf(int $distance): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add(
                $this->elements->dropEnd(1)->last()->withADistanceOf($distance)
            )
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Define the deepness range of the relationship
     *
     * @param int $min
     * @param int $max
     *
     * @throws LogicException If no relationship in the path
     *
     * @return self
     */
    public function withADistanceBetween(int $min, int $max): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add(
                $this->elements->dropEnd(1)->last()->withADistanceBetween($min, $max)
            )
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Define the minimum deepness of the relationship
     *
     * @param int $distance
     *
     * @throws LogicException If no relationship in the path
     *
     * @return self
     */
    public function withADistanceOfAtLeast(int $distance): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add(
                $this->elements->dropEnd(1)->last()->withADistanceOfAtLeast($distance)
            )
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Define the maximum deepness of the relationship
     *
     * @param int $distance
     *
     * @throws LogicException If no relationship in the path
     *
     * @return self
     */
    public function withADistanceOfAtMost(int $distance): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add(
                $this->elements->dropEnd(1)->last()->withADistanceOfAtMost($distance)
            )
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Define any deepness of the relationship
     *
     * @param int $distance
     *
     * @throws LogicException If no relationship in the path
     *
     * @return self
     */
    public function withAnyDistance(): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add(
                $this->elements->dropEnd(1)->last()->withAnyDistance()
            )
            ->add($this->elements->last());

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
            $element = $this->elements->dropEnd(1)->last();
        }

        $element = $element->withParameter($key, $value);
        $path = new self;
        $path->lastOperation = $this->lastOperation;

        if ($this->lastOperation === Node::class) {
            $path->elements = $this
                ->elements
                ->dropEnd(1)
                ->add($element);
        } else {
            $path->elements = $this
                ->elements
                ->dropEnd(2)
                ->add($element)
                ->add($this->elements->last());
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
            $element = $this->elements->dropEnd(1)->last();
        }

        $element = $element->withProperty($property, $cypher);
        $path = new self;
        $path->lastOperation = $this->lastOperation;

        if ($this->lastOperation === Node::class) {
            $path->elements = $this
                ->elements
                ->dropEnd(1)
                ->add($element);
        } else {
            $path->elements = $this
                ->elements
                ->dropEnd(2)
                ->add($element)
                ->add($this->elements->last());
        }

        return $path;
    }

    /**
     * Return all the parameters of the path
     *
     * @return MapInterface<string, Parameter>
     */
    public function parameters(): MapInterface
    {
        if ($this->parameters) {
            return $this->parameters;
        }

        return $this->parameters = $this->elements->reduce(
            new Map('string', Parameter::class),
            function(Map $carry, $element): Map {
                return $carry->merge($element->parameters());
            }
        );
    }

    public function __toString(): string
    {
        return (string) $this->elements->join('');
    }
}
