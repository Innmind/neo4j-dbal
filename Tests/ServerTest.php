<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $server = new Server('http', 'localhost', 7474);

        $this->assertSame('http', $server->getScheme());
        $this->assertSame('localhost', $server->getHost());
        $this->assertSame(7474, $server->getPort());
        $this->assertSame('http://localhost:7474/', (string) $server);
    }
}
