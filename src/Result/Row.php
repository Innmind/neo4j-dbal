<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

class Row implements RowInterface
{
    private $column;
    private $value;

    public function __construct(string $column, array $value)
    {
        $this->column = $column;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function value(): array
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