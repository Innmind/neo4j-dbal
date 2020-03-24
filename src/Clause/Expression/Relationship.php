<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Clause\Expression\Relationship\Distance,
    Exception\DomainException,
    Exception\LogicException,
};
use Innmind\Immutable\{
    Map,
    Str,
};
use function Innmind\Immutable\join;

final class Relationship
{
    public const LEFT = 'left';
    public const RIGHT = 'right';
    public const BOTH = 'both';

    private ?string $variable;
    private ?string $type;
    private string $direction;
    private Distance $distance;
    /** @var Map<string, Parameter> */
    private Map $parameters;
    /** @var Map<string, string> */
    private Map $properties;

    private function __construct(
        string $variable = null,
        string $type = null,
        string $direction = self::BOTH,
        Distance $distance = null
    ) {
        $this->variable = $variable;
        $this->type = $type;
        $this->direction = $direction;
        $this->distance = $distance ?? new Distance;
        /** @var Map<string, Parameter> */
        $this->parameters = Map::of('string', Parameter::class);
        /** @var Map<string, string> */
        $this->properties = Map::of('string', 'string');
    }

    /**
     * @param 'both'|'left'|'right' $direction
     */
    public static function of(
        string $direction,
        string $variable = null,
        string $type = null,
        Distance $distance = null
    ): self {
        switch ($direction) {
            case 'both':
                return self::both($variable, $type, $distance);

            case 'left':
                return self::left($variable, $type, $distance);

            case 'right':
                return self::right($variable, $type, $distance);
        }

        throw new LogicException("Unknown direction '$direction'");
    }

    public static function both(
        string $variable = null,
        string $type = null,
        Distance $distance = null
    ): self {
        return new self($variable, $type, self::BOTH, $distance);
    }

    public static function left(
        string $variable = null,
        string $type = null,
        Distance $distance = null
    ): self {
        return new self($variable, $type, self::LEFT, $distance);
    }

    public static function right(
        string $variable = null,
        string $type = null,
        Distance $distance = null
    ): self {
        return new self($variable, $type, self::RIGHT, $distance);
    }

    public function withADistanceOf(int $distance): self
    {
        $self = clone $this;
        $self->distance = Distance::of($distance);

        return $self;
    }

    public function withADistanceBetween(int $min, int $max): self
    {
        $self = clone $this;
        $self->distance = Distance::between($min, $max);

        return $self;
    }

    public function withADistanceOfAtLeast(int $distance): self
    {
        $self = clone $this;
        $self->distance = Distance::atLeast($distance);

        return $self;
    }

    public function withADistanceOfAtMost(int $distance): self
    {
        $self = clone $this;
        $self->distance = Distance::atMost($distance);

        return $self;
    }

    public function withAnyDistance(): self
    {
        $self = clone $this;
        $self->distance = Distance::any();

        return $self;
    }

    /**
     * @param mixed $value
     */
    public function withParameter(string $key, $value): self
    {
        if (Str::of($key)->empty()) {
            throw new DomainException;
        }

        $relationship = new self($this->variable, $this->type, $this->direction, $this->distance);
        $relationship->parameters = ($this->parameters)(
            $key,
            new Parameter($key, $value),
        );
        $relationship->properties = $this->properties;

        return $relationship;
    }

    public function withProperty(string $property, string $cypher): self
    {
        if (Str::of($property)->empty()) {
            throw new DomainException;
        }

        $relationship = new self($this->variable, $this->type, $this->direction, $this->distance);
        $relationship->parameters = $this->parameters;
        $relationship->properties = ($this->properties)($property, $cypher);

        return $relationship;
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
        $properties = '';

        if (!$this->properties->empty()) {
            $properties = sprintf(
                ' { %s }',
                join(
                    ', ',
                    $this
                        ->properties
                        ->map(function(string $property, string $value): string {
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
            '%s-[%s%s%s%s]-%s',
            $this->direction === self::LEFT ? '<' : '',
            (string) $this->variable,
            $this->type ? ':'.$this->type : '',
            $this->distance->cypher(),
            $properties,
            $this->direction === self::RIGHT ? '>' : '',
        );
    }
}
