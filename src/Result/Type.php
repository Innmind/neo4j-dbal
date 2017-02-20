<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

final class Type
{
    private $value;

    public function __construct(string $value)
    {
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
