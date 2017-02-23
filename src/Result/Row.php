<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Exception\InvalidArgumentException;

final class Row implements RowInterface
{
    private $column;
    private $value;

    public function __construct(string $column, $value)
    {
        if (empty($column)) {
            throw new InvalidArgumentException;
        }

        $this->column = $column;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function column(): string
    {
        return $this->column;
    }
}
