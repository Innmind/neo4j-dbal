<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause\UnwindClause;
use Innmind\Neo4j\DBAL\ClauseInterface;
use PHPUnit\Framework\TestCase;

class UnwindClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new UnwindClause('[1,1,2,2] as x');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('UNWIND', $c->identifier());
        $this->assertSame('[1,1,2,2] as x', (string) $c);
    }
}
