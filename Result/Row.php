<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

class Row implements RowInterface
{
    private $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function value(): array
    {
        return $this->value;
    }
}
