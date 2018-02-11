<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Server;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    public function testGetters()
    {
        $server = new Server('http', 'some-server', 7475);

        $this->assertSame('http', $server->scheme());
        $this->assertSame('some-server', $server->host());
        $this->assertSame(7475, $server->port());
        $this->assertSame('http://some-server:7475/', (string) $server);
    }

    public function testDefaults()
    {
        $this->assertSame('https://localhost:7474/', (string) new Server);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyScheme()
    {
        new Server('', 'localhost', 7474);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyHost()
    {
        new Server('http', '', 7474);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenInvalidPort()
    {
        new Server('http', 'localhost', 0);
    }
}
