<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
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
