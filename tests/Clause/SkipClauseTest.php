<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\SkipClause,
    ClauseInterface,
    Query\Parameter
};
use PHPUnit\Framework\TestCase;

class SkipClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new SkipClause('42');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('SKIP', $c->identifier());
        $this->assertSame('42', (string) $c);
    }
}
