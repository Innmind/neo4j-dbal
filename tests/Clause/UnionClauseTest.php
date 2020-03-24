<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\UnionClause,
    Clause,
};
use PHPUnit\Framework\TestCase;

class UnionClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new UnionClause;

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('UNION', $clause->identifier());
        $this->assertSame('', $clause->cypher());
    }
}
