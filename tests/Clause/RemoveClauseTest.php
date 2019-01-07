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
        $c = new RemoveClause('n:Foo');

        $this->assertInstanceOf(Clause::class, $c);
        $this->assertSame('REMOVE', $c->identifier());
        $this->assertSame('n:Foo', (string) $c);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new RemoveClause('');
    }
}
