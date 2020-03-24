<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Exception\DomainException,
};
use Innmind\Immutable\{
    Map,
    Set,
    Str,
};
use function Innmind\Immutable\{
    unwrap,
    join,
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
        $this->parameters = Map::of('string', Parameter::class);
        $this->properties = Map::of('string', 'string');
    }

    public function withParameter(string $key, $value): self
    {
        if (Str::of($key)->empty()) {
            throw new DomainException;
        }

        $node = new self($this->variable, unwrap($this->labels));
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

        $node = new self($this->variable, unwrap($this->labels));
        $node->parameters = $this->parameters;
        $node->properties = $this->properties->put($property, $cypher);

        return $node;
    }

    /**
     * @return Map<string, Parameter>
     */
    public function parameters(): Map
    {
        return $this->parameters;
    }

    public function __toString(): string
    {
        $labels = $properties = '';

        if ($this->labels->count() > 0) {
            $labels = ':'.join(':', $this->labels)->toString();
        }

        if ($this->properties->count() > 0) {
            $properties = sprintf(
                ' { %s }',
                join(
                    ', ',
                    $this
                        ->properties
                        ->map(function(string $property, string $value): string {
                            return sprintf(
                                '%s: %s',
                                $property,
                                $value
                            );
                        })
                        ->values(),
                )->toString(),
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
