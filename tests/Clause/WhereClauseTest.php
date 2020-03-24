<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\WhereClause,
    Clause,
    Clause\Parametrable,
    Query\Parameter,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class WhereClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new WhereClause('n.foo = {dumb}');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertInstanceOf(Parametrable::class, $clause);
        $this->assertSame('WHERE', $clause->identifier());
        $this->assertSame('n.foo = {dumb}', $clause->cypher());
        $this->assertNotSame($clause, $clause->withParameter('foo', 'bar'));
        $this->assertFalse($clause->hasParameters());
        $this->assertTrue($clause->withParameter('dumb', 'dumb')->hasParameters());
        $this->assertSame('string', (string) $clause->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $clause->parameters()->valueType());
        $this->assertCount(1, $clause->withParameter('dumb', 'dumb')->parameters());
    }

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        new WhereClause('');
    }

    public function testThrowWhenEmptyParameterKey()
    {
        $this->expectException(DomainException::class);

        (new WhereClause('foo'))->withParameter('', 'foo');
    }
}
