<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

final class Row implements RowInterface
{
    private $column;
    private $value;

    public function __construct(string $column, $value)
    {
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
