<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\OnMatchClause,
    Clause,
};
use PHPUnit\Framework\TestCase;

class OnMatchClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new OnMatchClause('SET n:Foo');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('ON MATCH', $clause->identifier());
        $this->assertSame('SET n:Foo', (string) $clause);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new OnMatchClause('');
    }
}
