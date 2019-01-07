<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\UsingClause,
    Clause,
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

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new UsingClause('');
    }
}
