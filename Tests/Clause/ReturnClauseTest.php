<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\ReturnClause;
use Innmind\Neo4j\DBAL\ClauseInterface;

class ReturnClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new ReturnClause('a', 'b', 'c', 'd');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('RETURN', $c->identifier());
        $this->assertSame('a, b, c, d', (string) $c);
    }
}
