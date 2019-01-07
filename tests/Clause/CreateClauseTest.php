<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\CreateClause,
    Clause\PathAware,
    Clause\Parametrable,
    Clause,
    Clause\Expression\Path,
    Clause\Expression\Relationship,
};
use PHPUnit\Framework\TestCase;

class CreateClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new CreateClause(Path::startWithNode(), false);

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertInstanceOf(PathAware::class, $clause);
        $this->assertInstanceOf(Parametrable::class, $clause);
        $this->assertSame('()', (string) $clause);
        $this->assertSame('CREATE', $clause->identifier());
        $this->assertSame(
            'CREATE UNIQUE',
            (new CreateClause(Path::startWithNode(), true))->identifier()
        );
    }

    public function testComposition()
    {
        $clause = (new CreateClause(Path::startWithNode('a', ['A']), false))
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
            (string) $clause
        );
        $this->assertTrue($clause->hasParameters());
        $this->assertCount(4, $clause->parameters());
        $this->assertInstanceOf(CreateClause::class, $clause);
    }
}
