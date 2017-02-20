<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\ForeachClause,
    ClauseInterface,
    Query\Parameter
};
use PHPUnit\Framework\TestCase;

class ForeachClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new ForeachClause('(n IN nodes(p)| SET n.marked = TRUE )');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('FOREACH', $c->identifier());
        $this->assertSame('(n IN nodes(p)| SET n.marked = TRUE )', (string) $c);
    }
}
