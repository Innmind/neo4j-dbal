<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\CreateClause;
use Innmind\Neo4j\DBAL\Clause\PathAwareInterface;
use Innmind\Neo4j\DBAL\Clause\ParametrableInterface;
use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Neo4j\DBAL\Clause\Expression\Path;
use Innmind\Neo4j\DBAL\Clause\Expression\Relationship;

class CreateClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new CreateClause(Path::startWithNode(), false);

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertInstanceOf(PathAwareInterface::class, $c);
        $this->assertInstanceOf(ParametrableInterface::class, $c);
        $this->assertSame('()', (string) $c);
        $this->assertSame('CREATE', $c->identifier());
        $this->assertSame(
            'CREATE UNIQUE',
            (new CreateClause(Path::startWithNode(), true))->identifier()
        );
    }

    public function testComposition()
    {
        $c = (new CreateClause(Path::startWithNode('a', ['A']), false))
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
        $this->assertInstanceOf(CreateClause::class, $c);
    }
}
