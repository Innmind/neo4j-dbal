<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Exception\DomainException;

final class Id
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new DomainException;
        }

        $this->value = $value;
    }

    /**
     * Return the id
     *
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Return the string representation of the id
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
