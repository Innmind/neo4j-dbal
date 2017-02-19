<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause\SetClause;
use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Neo4j\DBAL\Clause\ParametrableInterface;
use Innmind\Neo4j\DBAL\Query\Parameter;
use PHPUnit\Framework\TestCase;

class SetClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new SetClause('n.foo = {dumb}');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertInstanceOf(ParametrableInterface::class, $c);
        $this->assertSame('SET', $c->identifier());
        $this->assertSame('n.foo = {dumb}', (string) $c);
        $this->assertNotSame($c, $c->withParameter('foo', 'bar'));
        $this->assertFalse($c->hasParameters());
        $this->assertTrue($c->withParameter('dumb', 'dumb')->hasParameters());
        $this->assertSame(1, $c->withParameter('dumb', 'dumb')->parameters()->count());
        $this->assertSame(Parameter::class, $c->parameters()->getType());
    }
}
