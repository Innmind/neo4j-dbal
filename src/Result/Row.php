<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

interface Row
{
    /**
     * Return the row value
     *
     * @return scalar|array
     */
    public function value();

    /**
     * Return the column referencing this row
     */
    public function column(): string;
}
