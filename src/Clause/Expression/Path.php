<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Exception\LogicException,
};
use Innmind\Immutable\{
    Map,
    Sequence,
};
use function Innmind\Immutable\join;

final class Path
{
    /** @var Sequence<Node|Relationship> */
    private Sequence $elements;
    private ?string $lastOperation = null;

    private function __construct()
    {
        $this->elements = Sequence::of(Node::class.'|'.Relationship::class);
    }

    /**
     * Start the path with the given node
     *
     * @param list<string> $labels
     */
    public static function startWithNode(
        string $variable = null,
        array $labels = []
    ): self {
        $path = new self;
        $path->elements = ($path->elements)(
            new Node($variable, $labels),
        );
        $path->lastOperation = Node::class;

        return $path;
    }

    /**
     * Create a relationship to the given node
     *
     * @param list<string> $labels
     */
    public function linkedTo(string $variable = null, array $labels = []): self
    {
        $path = new self;
        $path->elements = $this
            ->elements
            ->add(Relationship::both())
            ->add(new Node($variable, $labels));
        $path->lastOperation = Node::class;

        return $path;
    }

    /**
     * Type the last declared relationship in the path
     *
     * @param 'both'|'left'|'right' $direction
     *
     * @throws LogicException If no relationship in the path
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = 'both'
    ): self {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = new self;
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add(Relationship::of($direction, $variable, $type))
            ->add($this->elements->last());
        $path->lastOperation = Relationship::class;

        return $path;
    }

    /**
     * Define the deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOf(int $distance): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        /**
         * @psalm-suppress PossiblyUndefinedMethod
         * @var Relationship
         */
        $relationship = $this->elements->dropEnd(1)->last()->withADistanceOf($distance);
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add($relationship)
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Define the deepness range of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceBetween(int $min, int $max): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        /**
         * @psalm-suppress PossiblyUndefinedMethod
         * @var Relationship
         */
        $relationship = $this->elements->dropEnd(1)->last()->withADistanceBetween($min, $max);
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add($relationship)
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Define the minimum deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOfAtLeast(int $distance): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        /**
         * @psalm-suppress PossiblyUndefinedMethod
         * @var Relationship
         */
        $relationship = $this->elements->dropEnd(1)->last()->withADistanceOfAtLeast($distance);
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add($relationship)
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Define the maximum deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOfAtMost(int $distance): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        /**
         * @psalm-suppress PossiblyUndefinedMethod
         * @var Relationship
         */
        $relationship = $this->elements->dropEnd(1)->last()->withADistanceOfAtMost($distance);
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add($relationship)
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Define any deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withAnyDistance(): self
    {
        if ($this->elements->size() < 3) {
            throw new LogicException;
        }

        $path = clone $this;
        /**
         * @psalm-suppress PossiblyUndefinedMethod
         * @var Relationship
         */
        $relationship = $this->elements->dropEnd(1)->last()->withAnyDistance();
        $path->elements = $this
            ->elements
            ->dropEnd(2)
            ->add($relationship)
            ->add($this->elements->last());

        return $path;
    }

    /**
     * Add the given parameter to the last operation
     *
     * @param mixed $value
     */
    public function withParameter(string $key, $value): self
    {
        if ($this->lastOperation === Node::class) {
            /** @var Node */
            $element = $this->elements->last();
        } else {
            /** @var Node */
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
     */
    public function withProperty(string $property, string $cypher): self
    {
        if ($this->lastOperation === Node::class) {
            /** @var Node */
            $element = $this->elements->last();
        } else {
            /** @var Node */
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
     * @return Map<string, Parameter>
     */
    public function parameters(): Map
    {
        /** @var Map<string, Parameter> */
        return $this->elements->reduce(
            Map::of('string', Parameter::class),
            function(Map $carry, $element): Map {
                return $carry->merge($element->parameters());
            },
        );
    }

    public function __toString(): string
    {
        return join(
            '',
            $this->elements->mapTo(
                'string',
                static fn($element): string => (string) $element,
            ),
        )->toString();
    }
}
