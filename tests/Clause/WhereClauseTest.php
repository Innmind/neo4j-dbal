<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\WhereClause,
    ClauseInterface,
    Clause\ParametrableInterface,
    Query\Parameter
};
use PHPUnit\Framework\TestCase;

class WhereClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new WhereClause('n.foo = {dumb}');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertInstanceOf(ParametrableInterface::class, $c);
        $this->assertSame('WHERE', $c->identifier());
        $this->assertSame('n.foo = {dumb}', (string) $c);
        $this->assertNotSame($c, $c->withParameter('foo', 'bar'));
        $this->assertFalse($c->hasParameters());
        $this->assertTrue($c->withParameter('dumb', 'dumb')->hasParameters());
        $this->assertSame('string', (string) $c->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $c->parameters()->valueType());
        $this->assertCount(1, $c->withParameter('dumb', 'dumb')->parameters());
    }
}
