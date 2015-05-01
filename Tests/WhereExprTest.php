<?php

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\WhereExpr;

class WhereExprTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleCondition()
    {
        $w = new WhereExpr;
        $w->andWhere('foo');

        $this->assertEquals('(foo)', (string) $w);
    }

    public function testOr()
    {
        $w = new WhereExpr;
        $w
            ->andWhere('foo')
            ->orWhere('bar');

        $this->assertEquals('(foo) OR (bar)', (string) $w);
    }

    public function testOrNot()
    {
        $w = new WhereExpr;
        $w
            ->andWhere('foo')
            ->orNotWhere('bar');

        $this->assertEquals('(foo) OR NOT (bar)', (string) $w);
    }

    public function testXor()
    {
        $w = new WhereExpr;
        $w
            ->andWhere('foo')
            ->xorWhere('bar');

        $this->assertEquals('(foo) XOR (bar)', (string) $w);
    }

    public function testAnd()
    {
        $w = new WhereExpr;
        $w
            ->andWhere('foo')
            ->andWhere('bar');

        $this->assertEquals('(foo) AND (bar)', (string) $w);
    }

    public function testAndNot()
    {
        $w = new WhereExpr;
        $w
            ->andWhere('foo')
            ->andNotWhere('bar');

        $this->assertEquals('(foo) AND NOT (bar)', (string) $w);
    }

    public function testSubExpr()
    {
        $w = new WhereExpr;
        $sub = new WhereExpr;
        $sub
            ->andWhere('foo')
            ->orWhere('bar');
        $w
            ->andWhere('baz')
            ->xorWhere($sub);

        $this->assertEquals(
            '(baz) XOR ((foo) OR (bar))',
            (string) $w
        );
    }
}
