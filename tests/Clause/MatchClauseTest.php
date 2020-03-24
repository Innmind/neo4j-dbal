<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\MatchClause,
    Clause\Parametrable,
    Clause\PathAware,
    Clause,
    Clause\Expression\Path,
    Clause\Expression\Relationship,
};
use PHPUnit\Framework\TestCase;

class MatchClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new MatchClause(Path::startWithNode());

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertInstanceOf(Parametrable::class, $clause);
        $this->assertInstanceOf(PathAware::class, $clause);
        $this->assertSame('()', $clause->cypher());
        $this->assertSame('MATCH', $clause->identifier());
    }

    public function testComposition()
    {
        $clause = (new MatchClause(Path::startWithNode('a', ['A'])))
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
            $clause->cypher(),
        );
        $this->assertTrue($clause->hasParameters());
        $this->assertCount(4, $clause->parameters());
        $this->assertInstanceOf(MatchClause::class, $clause);
    }
}
