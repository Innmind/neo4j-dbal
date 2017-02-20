<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\LimitClause,
    ClauseInterface,
    Query\Parameter
};
use PHPUnit\Framework\TestCase;

class LimitClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new LimitClause('42');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('LIMIT', $c->identifier());
        $this->assertSame('42', (string) $c);
    }
}
