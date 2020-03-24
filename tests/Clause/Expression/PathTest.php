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
        $path = Path::startWithNode('foo', ['Bar']);

        $this->assertInstanceOf(Path::class, $path);
        $this->assertSame('(foo:Bar)', $path->cypher());
        $this->assertNotSame($path, $path->withProperty('foo', ''));
        $this->assertInstanceOf(Path::class, $path->withProperty('foo', ''));
        $this->assertSame(
            '(foo:Bar { prop: {value} })',
            $path->withProperty('prop', '{value}')->cypher(),
        );
    }

    public function testLinkedTo()
    {
        $path = Path::startWithNode();

        $path2 = $path->linkedTo('bar', ['Baz']);
        $this->assertNotSame($path, $path2);
        $this->assertInstanceOf(Path::class, $path2);
        $this->assertSame('()', $path->cypher());
        $this->assertSame('()-[]-(bar:Baz)', $path2->cypher());
        $this->assertSame(
            '()-[]-(bar:Baz { prop: {value} })',
            $path2->withProperty('prop', '{value}')->cypher(),
        );
    }

    public function testThrowWhenNoRelationshipToType()
    {
        $this->expectException(LogicException::class);

        Path::startWithNode()->through();
    }

    public function testThrough()
    {
        $path = Path::startWithNode()->linkedTo();

        $path2 = $path->through(null, 'BAR');
        $this->assertNotSame($path, $path2);
        $this->assertInstanceOf(Path::class, $path2);
        $this->assertSame('()-[]-()', $path->cypher());
        $this->assertSame('()-[:BAR]-()', $path2->cypher());
        $this->assertSame(
            '()-[a:BAR { foo: {value} }]->()',
            $path
                ->through('a', 'BAR', Relationship::RIGHT)
                ->withProperty('foo', '{value}')
                ->cypher(),
        );
    }

    public function testWithADistanceOf()
    {
        $this->assertSame(
            '()-[*2]-()',
            Path::startWithNode()->linkedTo()->withADistanceOf(2)->cypher(),
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
            Path::startWithNode()->linkedTo()->withADistanceBetween(2, 3)->cypher(),
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
            Path::startWithNode()->linkedTo()->withADistanceOfAtLeast(2)->cypher(),
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
            Path::startWithNode()->linkedTo()->withADistanceOfAtMost(2)->cypher(),
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
            Path::startWithNode()->linkedTo()->withAnyDistance()->cypher(),
        );
    }

    public function testThrowWhenAnyDistanceWithoutRelationship()
    {
        $this->expectException(LogicException::class);

        Path::startWithNode()->withAnyDistance();
    }

    public function testParameters()
    {
        $path = Path::startWithNode();

        $path2 = $path->withParameter('foo', 'bar');
        $this->assertNotSame($path, $path2);
        $this->assertCount(1, $path2->parameters());
        $this->assertSame('string', (string) $path2->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $path2->parameters()->valueType());
    }

    public function testComplexPath()
    {
        $path = Path::startWithNode('a', ['A'])
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
            $path->cypher(),
        );
        $this->assertCount(4, $path->parameters());
    }
}
