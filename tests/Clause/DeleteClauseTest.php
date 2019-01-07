<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\DeleteClause,
    Clause,
    Query\Parameter,
};
use PHPUnit\Framework\TestCase;

class DeleteClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new DeleteClause('n', false);

        $this->assertInstanceOf(Clause::class, $c);
        $this->assertSame('DELETE', $c->identifier());
        $this->assertSame('n', (string) $c);

        $this->assertSame(
            'DETACH DELETE',
            (new DeleteClause('n', true))->identifier()
        );
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new DeleteClause('', false);
    }
}
