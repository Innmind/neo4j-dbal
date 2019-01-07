<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\OrderByClause,
    Clause,
};
use PHPUnit\Framework\TestCase;

class OrderByClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = OrderByClause::asc('n.foo');

        $this->assertInstanceOf(Clause::class, $c);
        $this->assertSame('ORDER BY', $c->identifier());
        $this->assertSame('n.foo ASC', (string) $c);
        $this->assertSame(
            'n.foo DESC',
            (string) OrderByClause::desc('n.foo')
        );
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        OrderByClause::asc('');
    }
}
