<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\ReturnClause,
    Clause,
};
use PHPUnit\Framework\TestCase;

class ReturnClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new ReturnClause('a', 'b', 'c', 'd');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('RETURN', $clause->identifier());
        $this->assertSame('a, b, c, d', (string) $clause);
    }
}
