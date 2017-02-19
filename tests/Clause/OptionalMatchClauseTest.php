<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause\OptionalMatchClause;
use Innmind\Neo4j\DBAL\Clause\ParametrableInterface;
use Innmind\Neo4j\DBAL\Clause\PathAwareInterface;
use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Neo4j\DBAL\Clause\Expression\Path;
use Innmind\Neo4j\DBAL\Clause\Expression\Relationship;

class OptionalMatchClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new OptionalMatchClause(Path::startWithNode());

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertInstanceOf(ParametrableInterface::class, $c);
        $this->assertInstanceOf(PathAwareInterface::class, $c);
        $this->assertSame('()', (string) $c);
        $this->assertSame('OPTIONAL MATCH', $c->identifier());
    }

    public function testComposition()
    {
        $c = (new OptionalMatchClause(Path::startWithNode('a', ['A'])))
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
            (string) $c
        );
        $this->assertTrue($c->hasParameters());
        $this->assertSame(4, $c->parameters()->count());
        $this->assertInstanceOf(OptionalMatchClause::class, $c);
    }
}