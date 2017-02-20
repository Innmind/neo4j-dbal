<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\Query\Parameter;
use Innmind\Immutable\{
    MapInterface,
    Map
};

class Relationship
{
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';
    const BOTH = 'BOTH';

    private $variable;
    private $type;
    private $direction;
    private $parameters;
    private $properties;

    public function __construct(
        string $variable = null,
        string $type = null,
        string $direction = self::BOTH
    ) {
        $this->variable = $variable;
        $this->type = $type;
        $this->direction = $direction;
        $this->parameters = new Map('string', Parameter::class);
        $this->properties = new Map('string', 'string');
    }

    public function withParameter(string $key, $value): self
    {
        $relationship = new self($this->variable, $this->type, $this->direction);
        $relationship->parameters = $this->parameters->put(
            $key,
            new Parameter($key, $value)
        );
        $relationship->properties = $this->properties;

        return $relationship;
    }

    public function withProperty(string $property, string $cypher): self
    {
        $relationship = new self($this->variable, $this->type, $this->direction);
        $relationship->parameters = $this->parameters;
        $relationship->properties = $this->properties->put($property, $cypher);

        return $relationship;
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
        $properties = '';

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
            '%s-[%s%s%s]-%s',
            $this->direction === self::LEFT ? '<' : null,
            $this->variable,
            $this->type ? ':'.$this->type : null,
            $properties,
            $this->direction === self::RIGHT ? '>' : null
        );
    }
}
