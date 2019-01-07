<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\LimitClause,
    Clause,
    Query\Parameter,
};
use PHPUnit\Framework\TestCase;

class LimitClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new LimitClause('42');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('LIMIT', $clause->identifier());
        $this->assertSame('42', (string) $clause);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyCypher()
    {
        new LimitClause('');
    }
}
