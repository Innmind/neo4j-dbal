<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause\ForeachClause;
use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Neo4j\DBAL\Query\Parameter;

class ForeachClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new ForeachClause('(n IN nodes(p)| SET n.marked = TRUE )');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('FOREACH', $c->identifier());
        $this->assertSame('(n IN nodes(p)| SET n.marked = TRUE )', (string) $c);
    }
}
