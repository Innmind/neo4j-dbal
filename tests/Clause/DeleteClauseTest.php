<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\DeleteClause,
    Clause,
    Query\Parameter,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class DeleteClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new DeleteClause('n', false);

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('DELETE', $clause->identifier());
        $this->assertSame('n', (string) $clause);

        $this->assertSame(
            'DETACH DELETE',
            (new DeleteClause('n', true))->identifier()
        );
    }

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        new DeleteClause('', false);
    }
}
