<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\UsingClause,
    Clause,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class UsingClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new UsingClause('INDEX n.foo');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('USING', $clause->identifier());
        $this->assertSame('INDEX n.foo', (string) $clause);
    }

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        new UsingClause('');
    }
}
