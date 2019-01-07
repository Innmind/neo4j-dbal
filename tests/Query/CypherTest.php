<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\{
    Query\Cypher,
    Query,
    Query\Parameter,
};
use PHPUnit\Framework\TestCase;

class CypherTest extends TestCase
{
    public function testInterface()
    {
        $cypher = new Cypher($expression = 'MATCH n RETURN n');

        $this->assertInstanceOf(Query::class, $cypher);
        $this->assertSame($expression, $cypher->cypher());
        $this->assertSame($expression, (string) $cypher);
    }

    public function testParameters()
    {
        $cypher = new Cypher('foo');

        $this->assertFalse($cypher->hasParameters());
        $cypher2 = $cypher->withParameters(['foo' => 'bar']);
        $this->assertNotSame($cypher, $cypher2);
        $this->assertInstanceOf(Query::class, $cypher2);
        $this->assertFalse($cypher->hasParameters());
        $this->assertTrue($cypher2->hasParameters());
        $this->assertCount(1, $cypher2->parameters());
        $this->assertSame('string', (string) $cypher2->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $cypher2->parameters()->valueType());
        $this->assertSame('string', (string) $cypher->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $cypher->parameters()->valueType());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new Cypher('');
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyParameterKey()
    {
        (new Cypher('foo'))->withParameter('', 'foo');
    }
}
