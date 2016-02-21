<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\OrderByClause;
use Innmind\Neo4j\DBAL\ClauseInterface;

class OrderByClauseTest extends \PHPUnit_Framework_TestCase
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
