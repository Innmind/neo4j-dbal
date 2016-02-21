<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\UnionClause;
use Innmind\Neo4j\DBAL\ClauseInterface;

class UnionClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new UnionClause;

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('UNION', $c->identifier());
        $this->assertSame('', (string) $c);
    }
}
