<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\OrderByClause,
    Clause,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class OrderByClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = OrderByClause::asc('n.foo');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('ORDER BY', $clause->identifier());
        $this->assertSame('n.foo ASC', $clause->cypher());
        $this->assertSame(
            'n.foo DESC',
            OrderByClause::desc('n.foo')->cypher(),
        );
    }

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        OrderByClause::asc('');
    }
}
