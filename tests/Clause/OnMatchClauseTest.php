<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\OnMatchClause,
    Clause,
    Exception\DomainException,
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

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        new OnMatchClause('');
    }
}
