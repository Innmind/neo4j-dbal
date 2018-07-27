<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Clause\Expression\Path,
    Clause\Expression\Relationship,
    Query\Parameter,
    Exception\LogicException,
};
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testStartWithNode()
    {
        $p = Path::startWithNode('foo', ['Bar']);

        $this->assertInstanceOf(Path::class, $p);
        $this->assertSame('(foo:Bar)', (string) $p);
        $this->assertNotSame($p, $p->withProperty('foo', ''));
        $this->assertInstanceOf(Path::class, $p->withProperty('foo', ''));
        $this->assertSame(
            '(foo:Bar { prop: {value} })',
            (string) $p->withProperty('prop', '{value}')
        );
    }

    public function testLinkedTo()
    {
        $p = Path::startWithNode();

        $p2 = $p->linkedTo('bar', ['Baz']);
        $this->assertNotSame($p, $p2);
        $this->assertInstanceOf(Path::class, $p2);
        $this->assertSame('()', (string) $p);
        $this->assertSame('()-[]-(bar:Baz)', (string) $p2);
        $this->assertSame(
            '()-[]-(bar:Baz { prop: {value} })',
            (string) $p2->withProperty('prop', '{value}')
        );
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\LogicException
     */
    public function testThrowWhenNoRelationshipToType()
    {
        Path::startWithNode()->through();
    }

    public function testThrough()
    {
        $p = Path::startWithNode()->linkedTo();

        $p2 = $p->through(null, 'BAR');
        $this->assertNotSame($p, $p2);
        $this->assertInstanceOf(Path::class, $p2);
        $this->assertSame('()-[]-()', (string) $p);
        $this->assertSame('()-[:BAR]-()', (string) $p2);
        $this->assertSame(
            '()-[a:BAR { foo: {value} }]->()',
            (string) $p
                ->through('a', 'BAR', Relationship::RIGHT)
                ->withProperty('foo', '{value}')
        );
    }

    public function testWithADistanceOf()
    {
        $this->assertSame(
            '()-[*2]-()',
            (string) Path::startWithNode()->linkedTo()->withADistanceOf(2)
        );
    }

    public function testThrowWhenADistanceOfWithoutRelationship()
    {
        $this->expectException(LogicException::class);

        Path::startWithNode()->withADistanceOf(2);
    }

    public function testWithADistanceBetween()
    {
        $this->assertSame(
            '()-[*2..3]-()',
            (string) Path::startWithNode()->linkedTo()->withADistanceBetween(2, 3)
        );
    }

    public function testThrowWhenADistanceBetweenWithoutRelationship()
    {
        $this->expectException(LogicException::class);

        Path::startWithNode()->withADistanceBetween(2, 3);
    }

    public function testWithADistanceOfAtLeast()
    {
        $this->assertSame(
            '()-[*2..]-()',
            (string) Path::startWithNode()->linkedTo()->withADistanceOfAtLeast(2)
        );
    }

    public function testThrowWhenADistanceOfAtLeastWithoutRelationship()
    {
        $this->expectException(LogicException::class);

        Path::startWithNode()->withADistanceOfAtLeast(2);
    }

    public function testWithADistanceOfMost()
    {
        $this->assertSame(
            '()-[*..2]-()',
            (string) Path::startWithNode()->linkedTo()->withADistanceOfAtMost(2)
        );
    }

    public function testThrowWhenADistanceOfAtMostWithoutRelationship()
    {
        $this->expectException(LogicException::class);

        Path::startWithNode()->withADistanceOfAtMost(2);
    }

    public function testWithAnyDistance()
    {
        $this->assertSame(
            '()-[*]-()',
            (string) Path::startWithNode()->linkedTo()->withAnyDistance()
        );
    }

    public function testThrowWhenAnyDistanceWithoutRelationship()
    {
        $this->expectException(LogicException::class);

        Path::startWithNode()->withAnyDistance();
    }

    public function testParameters()
    {
        $p = Path::startWithNode();

        $p2 = $p->withParameter('foo', 'bar');
        $this->assertNotSame($p, $p2);
        $this->assertCount(1, $p2->parameters());
        $this->assertSame('string', (string) $p2->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $p2->parameters()->valueType());
        $this->assertSame($p2->parameters(), $p2->parameters());
    }

    public function testComplexPath()
    {
        $p = Path::startWithNode('a', ['A'])
                ->withProperty('a', '{a}')
                ->withParameter('a', 'foo')
            ->linkedTo('b', ['B'])
                ->withProperty('b', '{b}')
                ->withParameter('b', 'bar')
            ->through(null, 'TYPE|ANOTHER', Relationship::RIGHT)
                ->withProperty('t', '{baz}')
                ->withParameter('baz', 'dont know')
            ->linkedTo()
            ->through('r', null, Relationship::LEFT)
                ->withProperty('r', '{wat}')
                ->withParameter('wat', 'ever');

        $this->assertSame(
            '(a:A { a: {a} })-[:TYPE|ANOTHER { t: {baz} }]->(b:B { b: {b} })<-[r { r: {wat} }]-()',
            (string) $p
        );
        $this->assertCount(4, $p->parameters());
    }
}
