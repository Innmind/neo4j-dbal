<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Exception\DomainException,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
    Set,
    Str,
};

final class Node
{
    private ?string $variable;
    private Set $labels;
    private Map $parameters;
    private Map $properties;

    public function __construct(string $variable = null, array $labels = [])
    {
        $this->variable = $variable;
        $this->labels = Set::of('string', ...$labels);;
        $this->parameters = new Map('string', Parameter::class);
        $this->properties = new Map('string', 'string');
    }

    public function withParameter(string $key, $value): self
    {
        if (Str::of($key)->empty()) {
            throw new DomainException;
        }

        $node = new self($this->variable, $this->labels->toPrimitive());
        $node->parameters = $this->parameters->put(
            $key,
            new Parameter($key, $value)
        );
        $node->properties = $this->properties;

        return $node;
    }

    public function withProperty(string $property, string $cypher): self
    {
        if (Str::of($property)->empty()) {
            throw new DomainException;
        }

        $node = new self($this->variable, $this->labels->toPrimitive());
        $node->parameters = $this->parameters;
        $node->properties = $this->properties->put($property, $cypher);

        return $node;
    }

    /**
     * @return MapInterface<string, Parameter>
     */
    public function parameters(): MapInterface
    {
        return $this->parameters;
    }

    public function __toString(): string
    {
        $labels = $properties = '';

        if ($this->labels->count() > 0) {
            $labels = ':'.$this->labels->join(':');
        }

        if ($this->properties->count() > 0) {
            $properties = sprintf(
                ' { %s }',
                $this
                    ->properties
                    ->map(function(string $property, string $value): string {
                        return sprintf(
                            '%s: %s',
                            $property,
                            $value
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
