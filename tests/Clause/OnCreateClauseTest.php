<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\OnCreateClause,
    ClauseInterface
};
use PHPUnit\Framework\TestCase;

class OnCreateClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new OnCreateClause('SET n:Foo');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('ON CREATE', $c->identifier());
        $this->assertSame('SET n:Foo', (string) $c);
    }
}
