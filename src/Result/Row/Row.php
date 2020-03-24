<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result\Row;

use Innmind\Neo4j\DBAL\{
    Result\Row as RowInterface,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

final class Row implements RowInterface
{
    private string $column;
    /** @var scalar|array */
    private $value;

    /**
     * @param scalar|array $value
     */
    public function __construct(string $column, $value)
    {
        if (Str::of($column)->empty()) {
            throw new DomainException;
        }

        $this->column = $column;
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function column(): string
    {
        return $this->column;
    }
}
