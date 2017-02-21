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
    ResultInterface
};
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    private $t;

    public function setUp()
    {
        $server = new Server(
            'http',
            'localhost',
            7474
        );
        $auth = new Authentication('neo4j', 'ci');
        $this->t = new Http(
            new HttpTranslator(
                new Transactions($server, $auth)
            ),
            $server,
            $auth
        );
    }

    public function testPing()
    {
        $this->assertSame($this->t, $this->t->ping());
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
        $t = new Http(
            new HttpTranslator(
                new Transactions($server, $auth)
            ),
            $server,
            $auth
        );

        $t->ping();
    }

    public function testExecute()
    {
        $q = $this->createMock(QueryInterface::class);
        $q
            ->method('cypher')
            ->willReturn('match (n) return n;');

        $r = $this->t->execute($q);

        $this->assertInstanceOf(ResultInterface::class, $r);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\QueryException
     * @expectedExceptionMessage The query failed to execute properly
     * @expectedExceptionCode 400
     */
    public function testThrowWhenQueryFailed()
    {
        $q = $this->createMock(QueryInterface::class);
        $q
            ->method('cypher')
            ->willReturn('foo');

        $this->t->execute($q);
    }
}
