<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

interface RowInterface
{
    /**
     * Return the row value
     *
     * @return array
     */
    public function value(): array;
}
