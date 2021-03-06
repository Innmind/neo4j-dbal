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
    /** @var Set<string> */
    private Set $labels;
    /** @var Map<string, Parameter> */
    private Map $parameters;
    /** @var Map<string, string> */
    private Map $properties;

    public function __construct(string $variable = null, string ...$labels)
    {
        $this->variable = $variable;
        $this->labels = Set::strings(...$labels);
        /** @var Map<string, Parameter> */
        $this->parameters = Map::of('string', Parameter::class);
        /** @var Map<string, string> */
        $this->properties = Map::of('string', 'string');
    }

    /**
     * @param mixed $value
     */
    public function withParameter(string $key, $value): self
    {
        if (Str::of($key)->empty()) {
            throw new DomainException;
        }

        $node = new self($this->variable, ...unwrap($this->labels));
        $node->parameters = ($this->parameters)(
            $key,
            new Parameter($key, $value),
        );
        $node->properties = $this->properties;

        return $node;
    }

    public function withProperty(string $property, string $cypher): self
    {
        if (Str::of($property)->empty()) {
            throw new DomainException;
        }

        $node = new self($this->variable, ...unwrap($this->labels));
        $node->parameters = $this->parameters;
        $node->properties = ($this->properties)($property, $cypher);

        return $node;
    }

    /**
     * @return Map<string, Parameter>
     */
    public function parameters(): Map
    {
        return $this->parameters;
    }

    public function cypher(): string
    {
        $labels = $properties = '';

        if (!$this->labels->empty()) {
            $labels = ':'.join(':', $this->labels)->toString();
        }

        if (!$this->properties->empty()) {
            $properties = \sprintf(
                ' { %s }',
                join(
                    ', ',
                    $this
                        ->properties
                        ->map(static function(string $property, string $value): string {
                            return \sprintf(
                                '%s: %s',
                                $property,
                                $value,
                            );
                        })
                        ->values(),
                )->toString(),
            );
        }

        return \sprintf(
            '(%s%s%s)',
            (string) $this->variable,
            $labels,
            $properties,
        );
    }
}
