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
        $c = new OrderByClause('n.foo', OrderByClause::ASC);

        $this->assertInstanceOf(Clause::class, $c);
        $this->assertSame('ORDER BY', $c->identifier());
        $this->assertSame('n.foo ASC', (string) $c);
        $this->assertSame(
            'n.foo DESC',
            (string) new OrderByClause('n.foo', OrderByClause::DESC)
        );
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new OrderByClause('', OrderByClause::ASC);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyDirection()
    {
        new OrderByClause('foo', '');
    }
}
