<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\SkipClause,
    Clause,
    Query\Parameter,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class SkipClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new SkipClause('42');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('SKIP', $clause->identifier());
        $this->assertSame('42', $clause->cypher());
    }

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        new SkipClause('');
    }
}
