<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Event;

use Innmind\Neo4j\DBAL\Event\PostQueryEvent;
use Innmind\Neo4j\DBAL\QueryInterface;
use Innmind\Neo4j\DBAL\ResultInterface;
use Innmind\Immutable\TypedCollectionInterface;

class PostQueryEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $q = new class implements QueryInterface {
            public function cypher(): string
            {
            }

            public function __toString(): string
            {
            }

            public function parameters(): TypedCollectionInterface
            {
            }

            public function hasParameters(): bool
            {
            }
        };
        $r = new class implements ResultInterface {
            public function nodes(): TypedCollectionInterface
            {
            }

            public function relationships(): TypedCollectionInterface
            {
            }

            public function rows(): TypedCollectionInterface
            {
            }
        };
        $e = new PostQueryEvent($q, $r);

        $this->assertSame($q, $e->query());
        $this->assertSame($r, $e->result());
    }
}
