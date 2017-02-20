<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\UsingClause,
    ClauseInterface
};
use PHPUnit\Framework\TestCase;

class UsingClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new UsingClause('INDEX n.foo');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('USING', $c->identifier());
        $this->assertSame('INDEX n.foo', (string) $c);
    }
}
