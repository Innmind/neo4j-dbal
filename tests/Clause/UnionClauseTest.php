<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\UnionClause,
    ClauseInterface
};
use PHPUnit\Framework\TestCase;

class UnionClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new UnionClause;

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('UNION', $c->identifier());
        $this->assertSame('', (string) $c);
    }
}
