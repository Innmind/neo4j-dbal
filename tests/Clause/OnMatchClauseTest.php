<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\OnMatchClause,
    Clause
};
use PHPUnit\Framework\TestCase;

class OnMatchClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new OnMatchClause('SET n:Foo');

        $this->assertInstanceOf(Clause::class, $c);
        $this->assertSame('ON MATCH', $c->identifier());
        $this->assertSame('SET n:Foo', (string) $c);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new OnMatchClause('');
    }
}
