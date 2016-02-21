<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\LimitClause;
use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Neo4j\DBAL\Query\Parameter;

class LimitClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new LimitClause('42');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('LIMIT', $c->identifier());
        $this->assertSame('42', (string) $c);
    }
}
