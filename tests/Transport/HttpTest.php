<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Transport;

use Innmind\Neo4j\DBAL\{
    Transport\Http,
    Translator\HttpTranslator,
    Transactions,
    Query,
    Result,
    HttpTransport\Transport as HttpTransport,
    Transport,
};
use Innmind\Url\Url;
use Innmind\TimeContinuum\TimeContinuumInterface;
use function Innmind\HttpTransport\bootstrap as http;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    private $transport;

    public function setUp()
    {
        $httpTransport = new HttpTransport(
            Url::fromString('http://neo4j:ci@localhost:7474/'),
            http()['default']()
        );
        $this->transport = new Http(
            new HttpTranslator(
                new Transactions(
                    $httpTransport,
                    $this->createMock(TimeContinuumInterface::class)
                )
            ),
            $httpTransport
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(
            Transport::class,
            $this->transport
        );
    }

    public function testPing()
    {
        $this->assertSame($this->transport, $this->transport->ping());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\ServerDown
     */
    public function testThrowWhenPingUnavailableServer()
    {
        $httpTransport = new HttpTransport(
            Url::fromString('http://neo4j:ci@localhost:1337/'),
            http()['default']()
        );
        $transport = new Http(
            new HttpTranslator(
                new Transactions(
                    $httpTransport,
                    $this->createMock(TimeContinuumInterface::class)
                )
            ),
            $httpTransport
        );

        $transport->ping();
    }

    public function testExecute()
    {
        $query = $this->createMock(Query::class);
        $query
            ->method('cypher')
            ->willReturn('match (n) return n;');

        $result = $this->transport->execute($query);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\QueryFailed
     * @expectedExceptionMessage The query failed to execute properly
     * @expectedExceptionCode 400
     */
    public function testThrowWhenQueryFailed()
    {
        $query = $this->createMock(Query::class);
        $query
            ->method('cypher')
            ->willReturn('foo');

        $this->transport->execute($query);
    }
}
