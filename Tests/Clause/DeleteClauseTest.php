<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\DeleteClause;
use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Neo4j\DBAL\Query\Parameter;

class DeleteClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new DeleteClause('n', false);

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('DELETE', $c->identifier());
        $this->assertSame('n', (string) $c);

        $this->assertSame(
            'DETACH DELETE',
            (new DeleteClause('n', true))->identifier()
        );
    }
}
