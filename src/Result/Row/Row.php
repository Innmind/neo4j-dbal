<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result\Row;

use Innmind\Neo4j\DBAL\{
    Result\Row as RowInterface,
    Exception\DomainException
};

final class Row implements RowInterface
{
    private $column;
    private $value;

    public function __construct(string $column, $value)
    {
        if (empty($column)) {
            throw new DomainException;
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
