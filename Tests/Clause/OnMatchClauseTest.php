<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\OnMatchClause;
use Innmind\Neo4j\DBAL\ClauseInterface;

class OnMatchClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new OnMatchClause('SET n:Foo');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('ON MATCH', $c->identifier());
        $this->assertSame('SET n:Foo', (string) $c);
    }
}
