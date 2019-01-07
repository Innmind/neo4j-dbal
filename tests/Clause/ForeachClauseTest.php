<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\ForeachClause,
    Clause,
    Query\Parameter,
};
use PHPUnit\Framework\TestCase;

class ForeachClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new ForeachClause('(n IN nodes(p)| SET n.marked = TRUE )');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('FOREACH', $clause->identifier());
        $this->assertSame('(n IN nodes(p)| SET n.marked = TRUE )', (string) $clause);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new ForeachClause('');
    }
}
