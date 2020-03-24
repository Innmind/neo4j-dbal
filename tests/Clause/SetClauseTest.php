<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\SetClause,
    Clause,
    Clause\Parametrable,
    Query\Parameter,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class SetClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new SetClause('n.foo = {dumb}');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertInstanceOf(Parametrable::class, $clause);
        $this->assertSame('SET', $clause->identifier());
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

        new SetClause('');
    }

    public function testThrowWhenEmptyParameterKey()
    {
        $this->expectException(DomainException::class);

        (new SetClause('foo'))->withParameter('', 'foo');
    }
}
