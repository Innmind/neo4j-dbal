<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause;

final class UnionClause implements Clause
{
    private const IDENTIFIER = 'UNION';

    public function identifier(): string
    {
        return self::IDENTIFIER;
    }

    public function __toString(): string
    {
        return '';
    }
}
