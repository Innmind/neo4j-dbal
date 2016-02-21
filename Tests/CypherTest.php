<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\Cypher;
use Innmind\Neo4j\DBAL\QueryInterface;
use Innmind\Neo4j\DBAL\Query\Parameter;

class CypherTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new Cypher($e = 'MATCH n RETURN n');

        $this->assertInstanceOf(QueryInterface::class, $c);
        $this->assertSame($e, $c->cypher());
        $this->assertSame($e, (string) $c);
    }

    public function testParameters()
    {
        $c = new Cypher('');

        $this->assertFalse($c->hasParameters());
        $c2 = $c->withParameters(['foo' => 'bar']);
        $this->assertNotSame($c, $c2);
        $this->assertInstanceOf(QueryInterface::class, $c2);
        $this->assertFalse($c->hasParameters());
        $this->assertTrue($c2->hasParameters());
        $this->assertSame(1, $c2->parameters()->count());
        $this->assertSame(Parameter::class, $c2->parameters()->getType());
        $this->assertSame(Parameter::class, $c->parameters()->getType());
    }
}
