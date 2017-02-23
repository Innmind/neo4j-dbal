<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\MergeClause,
    Clause\PathAwareInterface,
    Clause\ParametrableInterface,
    ClauseInterface,
    Clause\Expression\Path,
    Clause\Expression\Relationship
};
use PHPUnit\Framework\TestCase;

class MergeClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new MergeClause(Path::startWithNode());

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertInstanceOf(PathAwareInterface::class, $c);
        $this->assertInstanceOf(ParametrableInterface::class, $c);
        $this->assertSame('()', (string) $c);
        $this->assertSame('MERGE', $c->identifier());
    }

    public function testComposition()
    {
        $c = (new MergeClause(Path::startWithNode('a', ['A'])))
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
        $this->assertCount(4, $c->parameters());
        $this->assertInstanceOf(MergeClause::class, $c);
    }
}
