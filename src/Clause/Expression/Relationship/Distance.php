<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause\Expression\Relationship;

use Innmind\Neo4j\DBAL\Exception\DomainException;

final class Distance
{
    private ?int $min = 1;
    private ?int $max = 1;

    public static function of(int $distance): self
    {
        if ($distance < 1) {
            throw new DomainException((string) $distance);
        }

        $self = new self;
        $self->min = $distance;
        $self->max = $distance;

        return $self;
    }

    public static function between(int $min, int $max): self
    {
        if ($min < 0 || $max < $min) {
            throw new DomainException;
        }

        $self = new self;
        $self->min = $min;
        $self->max = $max;

        return $self;
    }

    public static function atLeast(int $distance): self
    {
        if ($distance < 1) {
            throw new DomainException((string) $distance);
        }

        $self = new self;
        $self->min = $distance;
        $self->max = null;

        return $self;
    }

    public static function atMost(int $distance): self
    {
        if ($distance < 2) {
            throw new DomainException((string) $distance);
        }

        $self = new self;
        $self->min = null;
        $self->max = $distance;

        return $self;
    }

    public static function any(): self
    {
        $self = new self;
        $self->min = null;
        $self->max = null;

        return $self;
    }

    public function __toString(): string
    {
        if ($this->min === null && $this->max === null) {
            return '*';
        }

        if ($this->min !== $this->max) {
            return "*{$this->min}..{$this->max}";
        }

        if ($this->min === 1) {
            return '';
        }

        return '*'.$this->min;
    }
}
