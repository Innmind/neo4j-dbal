<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause\Expression;

use Innmind\Neo4j\DBAL\Clause\Expression\Path;
use Innmind\Neo4j\DBAL\Clause\Expression\Relationship;
use Innmind\Neo4j\DBAL\Query\Parameter;

class PathTest extends \PHPUnit_Framework_TestCase
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

    public function testParameters()
    {
        $p = Path::startWithNode();

        $p2 = $p->withParameter('foo', 'bar');
        $this->assertNotSame($p, $p2);
        $this->assertSame(1, $p2->parameters()->count());
        $this->assertSame(Parameter::class, $p2->parameters()->getType());
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
        $this->assertSame(4, $p->parameters()->count());
    }
}
