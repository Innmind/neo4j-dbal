<?php

namespace Tests\Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\{
    Query\Query,
    Query as QueryInterface,
    Exception\NonParametrableClause,
    Exception\NonPathAwareClause,
    Exception\NonMergeClause,
};
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(QueryInterface::class, new Query);
    }

    public function testBuilder()
    {
        //this is not a valid query obviously
        $query = (new Query)
            ->match('n', 'labels')
                ->withProperties(['foo' => '{foo}'])
                ->withParameters(['foo' => 'bar'])
            ->linkedTo('n2', 'labels')
                ->withProperties(['bar' => '{bar}'])
                ->withParameters(['bar' => 'baz'])
            ->through('TYPE', 'r')
                ->withProperties(['foo' => '{baz}'])
                ->withParameters(['baz' => 'foobar'])
                ->withADistanceOfAtMost(42)
            ->with('n', 'n2', 'r')
            ->where('n.foo = {foobar}')
                ->withParameter('foobar', 'baz')
            ->where('n2.bar = {foobaz}.whatever')
                ->withParameter('foobaz', ['whatever' => 'value'])
            ->set('n :ExtraLabel')
            ->create('n2', 'Foo', 'Bar')
            ->createUnique('n42', 'Baz')
            ->delete('unknown')
            ->remove('n.foo')
            ->foreach('(n IN nodes(p)| SET n.marked = TRUE )')
            ->limit('42')
            ->merge('n3')
            ->linkedTo()
            ->onCreate('SET n3.foo = "bar"')
            ->onMatch('SET n.updated = timestamp()')
            ->orderBy('n3.updated', 'desc')
            ->return('n', 'n2', 'n3')
            ->skip('3')
            ->unwind('[1,2,3] AS x')
            ->using('INDEX n.foo');

        $this->assertSame(
            $expression = 'MATCH (n:labels { foo: {foo} })-[r:TYPE*..42 { foo: {baz} }]-(n2:labels { bar: {bar} }) WITH n, n2, r WHERE n.foo = {foobar}, n2.bar = {foobaz}.whatever SET n :ExtraLabel CREATE (n2:Foo:Bar) CREATE UNIQUE (n42:Baz) DELETE unknown REMOVE n.foo FOREACH (n IN nodes(p)| SET n.marked = TRUE ) LIMIT 42 MERGE (n3)-[]-() ON CREATE SET n3.foo = "bar" ON MATCH SET n.updated = timestamp() ORDER BY n3.updated DESC RETURN n, n2, n3 SKIP 3 UNWIND [1,2,3] AS x USING INDEX n.foo',
            $query->cypher()
        );
        $this->assertSame($expression, $query->cypher());
        $this->assertCount(5, $query->parameters());
    }

    public function testThrowWhenApplyingParameterOnNonParametrableClause()
    {
        $this->expectException(NonParametrableClause::class);

        (new Query)
            ->delete('n')
            ->withParameter('foo', 'bar');
    }

    public function testThrowWhenApplyingPropertyOnNonPathAwareClause()
    {
        $this->expectException(NonPathAwareClause::class);

        (new Query)
            ->delete('n')
            ->withProperty('foo', 'foo');
    }

    public function testThrowWhenApplyingLinkedToOnNonPathAwareClause()
    {
        $this->expectException(NonPathAwareClause::class);

        (new Query)
            ->delete('n')
            ->linkedTo();
    }

    public function testThrowWhenApplyingTypedRelationshipOnNonPathAwareClause()
    {
        $this->expectException(NonPathAwareClause::class);

        (new Query)
            ->delete('n')
            ->through('r');
    }

    public function testThrowWhenApplyingADistanceOfOnNonPathAwareClause()
    {
        $this->expectException(NonPathAwareClause::class);

        (new Query)
            ->delete('n')
            ->withADistanceOf(2);
    }

    public function testThrowWhenApplyingADistanceBetweenOnNonPathAwareClause()
    {
        $this->expectException(NonPathAwareClause::class);

        (new Query)
            ->delete('n')
            ->withADistanceBetween(2, 3);
    }

    public function testThrowWhenApplyingADistanceOfAtLeastOnNonPathAwareClause()
    {
        $this->expectException(NonPathAwareClause::class);

        (new Query)
            ->delete('n')
            ->withADistanceOfAtLeast(2);
    }

    public function testThrowWhenApplyingADistanceOfAtMostOnNonPathAwareClause()
    {
        $this->expectException(NonPathAwareClause::class);

        (new Query)
            ->delete('n')
            ->withADistanceOfAtMost(2);
    }

    public function testThrowWhenApplyingAnyDistanceOnNonPathAwareClause()
    {
        $this->expectException(NonPathAwareClause::class);

        (new Query)
            ->delete('n')
            ->withAnyDistance();
    }

    public function testThrowWhenApplyingOnMatchOnNonMergeClause()
    {
        $this->expectException(NonMergeClause::class);

        (new Query)
            ->delete('n')
            ->onMatch('SET foo.updated = timestamp()');
    }

    public function testThrowWhenApplyingOnCreateOnNonMergeClause()
    {
        $this->expectException(NonMergeClause::class);

        (new Query)
            ->delete('n')
            ->onCreate('SET foo.created = timestamp()');
    }
}
