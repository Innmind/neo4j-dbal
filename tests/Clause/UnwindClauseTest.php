<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\UnwindClause,
    Clause
};
use PHPUnit\Framework\TestCase;

class UnwindClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new UnwindClause('[1,1,2,2] as x');

        $this->assertInstanceOf(Clause::class, $c);
        $this->assertSame('UNWIND', $c->identifier());
        $this->assertSame('[1,1,2,2] as x', (string) $c);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyCypher()
    {
        new UnwindClause('');
    }
}
