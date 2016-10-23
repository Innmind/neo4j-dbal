<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause\WithClause;
use Innmind\Neo4j\DBAL\ClauseInterface;

class WithClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new WithClause('a', 'b', 'c', 'd');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('WITH', $c->identifier());
        $this->assertSame('a, b, c, d', (string) $c);
    }
}
