<?php

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\Query;
use Innmind\Neo4j\DBAL\CypherBuilder;

class CypherBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected $builder;

    public function setUp()
    {
        $this->builder = new CypherBuilder;
    }

    public function testMatchReturn()
    {
        $q = new Query;
        $q
            ->match('(a:Foo)')
            ->setReturn('a');

        $this->assertEquals(
            'MATCH (a:Foo)' . "\n" . 'RETURN a;',
            $this->builder->getCypher($q)
        );
    }

    public function testAllKeysAtOnce()
    {
        $q = new Query;
        $q
            ->match('(a:Foo)')
            ->optionalMatch('(b:Bar)')
            ->where('a.foo = "bar"')
            ->create('(c:Baz)')
            ->merge('(d:FooBar)')
            ->onMatch('SET d.time = timestamp()')
            ->onCreate('SET d.time = timestamp()')
            ->set('d.foo = "bar"')
            ->delete('a')
            ->remove('b.foo')
            ->setReturn('c, d');

        $this->assertEquals(
            'MATCH (a:Foo)' . "\n" .
            'OPTIONAL MATCH (b:Bar)' . "\n" .
            'WHERE a.foo = "bar"' . "\n" .
            'CREATE (c:Baz)' . "\n" .
            'MERGE (d:FooBar)' . "\n" .
            'ON MATCH SET d.time = timestamp()' . "\n" .
            'ON CREATE SET d.time = timestamp()' . "\n" .
            'SET d.foo = "bar"' . "\n" .
            'DELETE a' . "\n" .
            'REMOVE b.foo' . "\n" .
            'RETURN c, d;',
            $this->builder->getCypher($q)
        );
    }

    public function testSetMultipleTimeSameSequenceKey()
    {
        $q = new Query;
        $q
            ->match('(a:Foo)')
            ->match('(b:Bar)')
            ->setReturn('a')
            ->setReturn('b');

        $this->assertEquals(
            'MATCH (a:Foo), (b:Bar)' . "\n" . 'RETURN a, b;',
            $this->builder->getCypher($q)
        );
    }

    public function testSetMultipleTimeSameSequenceKeySeparatedByAnotherOne()
    {
        $q = new Query;
        $q
            ->match('(a:Foo)')
            ->set('a.foo = "bar"')
            ->set('a.baz = "foo"')
            ->create('(b:Bar)')
            ->set('b += {a}')
            ->setReturn('b');

        $this->assertEquals(
            'MATCH (a:Foo)' . "\n" .
            'SET a.foo = "bar", a.baz = "foo"' . "\n" .
            'CREATE (b:Bar)' . "\n" .
            'SET b += {a}' . "\n" .
            'RETURN b;',
            $this->builder->getCypher($q)
        );
    }

    public function testFormatWhereExpr()
    {
        $q = new Query;
        $where = $q->createWhereExpr();
        $where
            ->andWhere('foo')
            ->orWhere('bar');
        $q
            ->match('(a:Foo)')
            ->where($where);

        $this->assertEquals(
            'MATCH (a:Foo)' . "\n" .
            'WHERE (foo) OR (bar);',
            $this->builder->getCypher($q)
        );
    }
}
