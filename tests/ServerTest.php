<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Server;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    public function testGetters()
    {
        $server = new Server('http', 'localhost', 7474);

        $this->assertSame('http', $server->scheme());
        $this->assertSame('localhost', $server->host());
        $this->assertSame(7474, $server->port());
        $this->assertSame('http://localhost:7474/', (string) $server);
    }
}
