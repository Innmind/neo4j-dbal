<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Event;

use Innmind\Neo4j\DBAL\{
    Event\PostQueryEvent,
    QueryInterface,
    ResultInterface
};
use Innmind\Immutable\{
    MapInterface,
    StreamInterface
};
use PHPUnit\Framework\TestCase;

class PostQueryEventTest extends TestCase
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

            public function parameters(): MapInterface
            {
            }

            public function hasParameters(): bool
            {
            }
        };
        $r = new class implements ResultInterface {
            public function nodes(): MapInterface
            {
            }

            public function relationships(): MapInterface
            {
            }

            public function rows(): StreamInterface
            {
            }
        };
        $e = new PostQueryEvent($q, $r);

        $this->assertSame($q, $e->query());
        $this->assertSame($r, $e->result());
    }
}
