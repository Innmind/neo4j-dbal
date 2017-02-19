<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause\ReturnClause;
use Innmind\Neo4j\DBAL\ClauseInterface;
use PHPUnit\Framework\TestCase;

class ReturnClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new ReturnClause('a', 'b', 'c', 'd');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('RETURN', $c->identifier());
        $this->assertSame('a, b, c, d', (string) $c);
    }
}
