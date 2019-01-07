<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\RemoveClause,
    Clause,
};
use PHPUnit\Framework\TestCase;

class RemoveClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new RemoveClause('n:Foo');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('REMOVE', $clause->identifier());
        $this->assertSame('n:Foo', (string) $clause);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new RemoveClause('');
    }
}
