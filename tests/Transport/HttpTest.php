<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Transport;

use Innmind\Neo4j\DBAL\{
    Transport\Http,
    Translator\HttpTranslator,
    Server,
    Authentication,
    Transactions,
    QueryInterface,
    ResultInterface,
    HttpTransport\Transport,
    TransportInterface
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\HttpTransport\GuzzleTransport;
use Innmind\Http\{
    Translator\Response\Psr7Translator,
    Factory\Header\Factories
};
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    private $transport;

    public function setUp()
    {
        $server = new Server(
            'http',
            'localhost',
            7474
        );
        $auth = new Authentication('neo4j', 'ci');
        $httpTransport = new Transport(
            $server,
            $auth,
            new GuzzleTransport(
                new Client,
                new Psr7Translator(
                    Factories::default()
                )
            )
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
            TransportInterface::class,
            $this->transport
        );
    }

    public function testPing()
    {
        $this->assertSame($this->transport, $this->transport->ping());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\ServerDownException
     */
    public function testThrowWhenPingUnavailableServer()
    {
        $server = new Server(
            'http',
            'localhost',
            1337
        );
        $auth = new Authentication('neo4j', 'ci');
        $httpTransport = new Transport(
            $server,
            $auth,
            new GuzzleTransport(
                new Client,
                new Psr7Translator(
                    Factories::default()
                )
            )
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
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('cypher')
            ->willReturn('match (n) return n;');

        $result = $this->transport->execute($query);

        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\QueryException
     * @expectedExceptionMessage The query failed to execute properly
     * @expectedExceptionCode 400
     */
    public function testThrowWhenQueryFailed()
    {
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('cypher')
            ->willReturn('foo');

        $this->transport->execute($query);
    }
}
