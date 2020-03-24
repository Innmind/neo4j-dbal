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
    Exception\ServerDown,
    Exception\QueryFailed,
};
use Innmind\Url\Url;
use Innmind\TimeContinuum\Clock;
use function Innmind\HttpTransport\bootstrap as http;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    private $transport;

    public function setUp(): void
    {
        $httpTransport = new HttpTransport(
            Url::of('http://neo4j:ci@localhost:7474/'),
            http()['default']()
        );
        $this->transport = new Http(
            new HttpTranslator(
                new Transactions(
                    $httpTransport,
                    $this->createMock(Clock::class)
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
        $this->assertNull($this->transport->ping());
    }

    public function testThrowWhenPingUnavailableServer()
    {
        $httpTransport = new HttpTransport(
            Url::of('http://neo4j:ci@localhost:1337/'),
            http()['default']()
        );
        $transport = new Http(
            new HttpTranslator(
                new Transactions(
                    $httpTransport,
                    $this->createMock(Clock::class)
                )
            ),
            $httpTransport
        );

        $this->expectException(ServerDown::class);

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

    public function testThrowWhenQueryFailed()
    {
        $query = $this->createMock(Query::class);
        $query
            ->method('cypher')
            ->willReturn('foo');

        $this->expectException(QueryFailed::class);
        $this->expectExceptionMessage('The query failed to execute properly');
        $this->expectExceptionCode(400);

        $this->transport->execute($query);
    }
}
