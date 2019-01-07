<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\OnCreateClause,
    Clause,
};
use PHPUnit\Framework\TestCase;

class OnCreateClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new OnCreateClause('SET n:Foo');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('ON CREATE', $clause->identifier());
        $this->assertSame('SET n:Foo', (string) $clause);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new OnCreateClause('');
    }
}
