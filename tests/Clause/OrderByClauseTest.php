<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\OrderByClause,
    ClauseInterface
};
use PHPUnit\Framework\TestCase;

class OrderByClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new OrderByClause('n.foo', OrderByClause::ASC);

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('ORDER BY', $c->identifier());
        $this->assertSame('n.foo ASC', (string) $c);
        $this->assertSame(
            'n.foo DESC',
            (string) new OrderByClause('n.foo', OrderByClause::DESC)
        );
    }
}
