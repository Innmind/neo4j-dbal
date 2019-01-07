<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\UnwindClause,
    Clause,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class UnwindClauseTest extends TestCase
{
    public function testInterface()
    {
        $clause = new UnwindClause('[1,1,2,2] as x');

        $this->assertInstanceOf(Clause::class, $clause);
        $this->assertSame('UNWIND', $clause->identifier());
        $this->assertSame('[1,1,2,2] as x', (string) $clause);
    }

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        new UnwindClause('');
    }
}
