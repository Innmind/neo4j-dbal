<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\Query\Parameter;
use Innmind\Immutable\TypedCollection;
use Innmind\Immutable\Collection;
use Innmind\Immutable\TypedCollectionInterface;

class Node
{
    private $variable;
    private $labels;
    private $parameters;
    private $properties;

    public function __construct(string $variable = null, array $labels = [])
    {
        $this->variable = $variable;
        $this->labels = new Collection($labels);
        $this->parameters = new TypedCollection(Parameter::class, []);
        $this->properties = new Collection([]);
    }

    public function withParameter(string $key, $value): self
    {
        $node = new self($this->variable, $this->labels->toPrimitive());
        $node->parameters = $this->parameters->push(
            new Parameter($key, $value)
        );
        $node->properties = $this->properties;

        return $node;
    }

    public function withProperty(string $property, string $cypher): self
    {
        $node = new self($this->variable, $this->labels->toPrimitive());
        $node->parameters = $this->parameters;
        $node->properties = $this->properties->set($property, $cypher);

        return $node;
    }

    public function parameters(): TypedCollectionInterface
    {
        return $this->parameters;
    }

    public function __toString(): string
    {
        $labels = $properties = '';

        if ($this->labels->count() > 0) {
            $labels = ':' . $this->labels->join(':');
        }

        if ($this->properties->count() > 0) {
            $properties = sprintf(
                ' { %s }',
                $this
                    ->properties
                    ->walk(function(&$element, $index) {
                        $element = sprintf(
                            '%s: %s',
                            $index,
                            $element
                        );
                    })
                    ->join(', ')
            );
        }

        return sprintf(
            '(%s%s%s)',
            $this->variable,
            $labels,
            $properties
        );
    }
}
