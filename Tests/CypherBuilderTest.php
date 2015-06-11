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
            'MATCH (a:Foo) RETURN a;',
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
            ->with('foo')
            ->orderBy('bar')
            ->skip('42')
            ->limit('42')
            ->unwind('[1,2] as x')
            ->union('')
            ->using('INDEX a(foo)')
            ->setReturn('c, d');

        $this->assertEquals(
            'MATCH (a:Foo) ' .
            'OPTIONAL MATCH (b:Bar) ' .
            'WHERE a.foo = "bar" ' .
            'CREATE (c:Baz) ' .
            'MERGE (d:FooBar) ' .
            'ON MATCH SET d.time = timestamp() ' .
            'ON CREATE SET d.time = timestamp() ' .
            'SET d.foo = "bar" ' .
            'DELETE a ' .
            'REMOVE b.foo ' .
            'WITH foo ' .
            'ORDER BY bar ' .
            'SKIP 42 ' .
            'LIMIT 42 ' .
            'UNWIND [1,2] as x ' .
            'UNION  ' .
            'USING INDEX a(foo) ' .
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
            'MATCH (a:Foo), (b:Bar) RETURN a, b;',
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
            'MATCH (a:Foo) ' .
            'SET a.foo = "bar", a.baz = "foo" ' .
            'CREATE (b:Bar) ' .
            'SET b += {a} ' .
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
            'MATCH (a:Foo) ' .
            'WHERE (foo) OR (bar);',
            $this->builder->getCypher($q)
        );
    }
}
