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
        $this->assertSame('n.foo ASC', (string) $clause);
        $this->assertSame(
            'n.foo DESC',
            (string) OrderByClause::desc('n.foo')
        );
    }

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        OrderByClause::asc('');
    }
}
