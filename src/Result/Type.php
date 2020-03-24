<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Exception\DomainException;
use Innmind\Immutable\Str;

final class Type
{
    private string $value;

    public function __construct(string $value)
    {
        if (Str::of($value)->empty()) {
            throw new DomainException;
        }

        $this->value = $value;
    }

    /**
     * Return the relationship type
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @see self::getValue
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
