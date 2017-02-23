<?php

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    Query,
    QueryInterface
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
        $q = (new Query)
            ->match('n', ['labels'])
                ->withProperties(['foo' => '{foo}'])
                ->withParameters(['foo' => 'bar'])
            ->linkedTo('n2', ['labels'])
                ->withProperties(['bar' => '{bar}'])
                ->withParameters(['bar' => 'baz'])
            ->through('TYPE', 'r')
                ->withProperties(['foo' => '{baz}'])
                ->withParameters(['baz' => 'foobar'])
            ->with('n', 'n2', 'r')
            ->where('n.foo = {foobar}')
                ->withParameter('foobar', 'baz')
            ->where('n2.bar = {foobaz}.whatever')
                ->withParameter('foobaz', ['whatever' => 'value'])
            ->set('n :ExtraLabel')
            ->create('n2', ['Foo', 'Bar'])
            ->delete('unknown')
            ->remove('n.foo')
            ->foreach('(n IN nodes(p)| SET n.marked = TRUE )')
            ->limit('42')
            ->merge('n3')
            ->linkedTo()
            ->onCreate('SET n3.foo = "bar"')
            ->onMatch('SET n.updated = timestamp()')
            ->orderBy('n3.updated', 'DESC')
            ->return('n', 'n2', 'n3')
            ->skip('3')
            ->unwind('[1,2,3] AS x')
            ->using('INDEX n.foo');

        $this->assertSame(
            $e = 'MATCH (n:labels { foo: {foo} })-[r:TYPE { foo: {baz} }]-(n2:labels { bar: {bar} }) WITH n, n2, r WHERE n.foo = {foobar}, n2.bar = {foobaz}.whatever SET n :ExtraLabel CREATE (n2:Foo:Bar) DELETE unknown REMOVE n.foo FOREACH (n IN nodes(p)| SET n.marked = TRUE ) LIMIT 42 MERGE (n3)-[]-() ON CREATE SET n3.foo = "bar" ON MATCH SET n.updated = timestamp() ORDER BY n3.updated DESC RETURN n, n2, n3 SKIP 3 UNWIND [1,2,3] AS x USING INDEX n.foo',
            (string) $q
        );
        $this->assertSame($e, $q->cypher());
        $this->assertCount(5, $q->parameters());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\NonParametrableClauseException
     */
    public function testThrowWhenApplyingParameterOnNonParametrableClause()
    {
        (new Query)
            ->delete('n')
            ->withParameter('foo', 'bar');
    }

    /**
     * @expectedException Innmind\neo4j\DBAL\Exception\NonPathAwareClauseException
     */
    public function testThrowWhenApplyingPropertyOnNonPathAwareClause()
    {
        (new Query)
            ->delete('n')
            ->withProperty('foo', 'foo');
    }

    /**
     * @expectedException Innmind\neo4j\DBAL\Exception\NonPathAwareClauseException
     */
    public function testThrowWhenApplyingLinkedToOnNonPathAwareClause()
    {
        (new Query)
            ->delete('n')
            ->linkedTo();
    }

    /**
     * @expectedException Innmind\neo4j\DBAL\Exception\NonPathAwareClauseException
     */
    public function testThrowWhenApplyingTypedRelationshipOnNonPathAwareClause()
    {
        (new Query)
            ->delete('n')
            ->through('r');
    }

    /**
     * @expectedException Innmind\neo4j\DBAL\Exception\NonMergeClauseException
     */
    public function testThrowWhenApplyingOnMatchOnNonMergeClause()
    {
        (new Query)
            ->delete('n')
            ->onMatch('SET foo.updated = timestamp()');
    }

    /**
     * @expectedException Innmind\neo4j\DBAL\Exception\NonMergeClauseException
     */
    public function testThrowWhenApplyingOnCreateOnNonMergeClause()
    {
        (new Query)
            ->delete('n')
            ->onCreate('SET foo.created = timestamp()');
    }
}
