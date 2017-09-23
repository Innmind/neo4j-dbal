<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Connection;

use Innmind\Neo4j\DBAL\{
    Connection\Connection,
    Connection as ConnectionInterface,
    Server,
    Authentication,
    Transactions,
    Transport\Http,
    Result,
    Translator\HttpTranslator,
    Query,
    HttpTransport\Transport
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\HttpTransport\GuzzleTransport;
use Innmind\Http\{
    Translator\Response\Psr7Translator,
    Factory\Header\Factories
};
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    private $c;

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
        $transactions = new Transactions(
            $httpTransport,
            $this->createMock(TimeContinuumInterface::class)
        );
        $this->c = new Connection(
            new Http(
                new HttpTranslator($transactions),
                $httpTransport
            ),
            $transactions
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(ConnectionInterface::class, $this->c);
    }

    public function testExecute()
    {
        $q = $this->createMock(Query::class);
        $q
            ->method('cypher')
            ->willReturn('match (n) return n');

        $r = $this->c->execute($q);

        $this->assertInstanceOf(Result::class, $r);
    }

    public function testTransactions()
    {
        $this->assertFalse($this->c->isTransactionOpened());
        $this->assertSame($this->c, $this->c->openTransaction());
        $this->assertTrue($this->c->isTransactionOpened());
        $this->assertSame($this->c, $this->c->commit());
        $this->assertFalse($this->c->isTransactionOpened());
        $this->c->openTransaction();
        $this->assertTrue($this->c->isTransactionOpened());
        $this->assertSame($this->c, $this->c->rollback());
        $this->assertFalse($this->c->isTransactionOpened());
    }

    public function testAlive()
    {
        $this->assertTrue($this->c->isAlive());

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
        $transactions = new Transactions(
            $httpTransport,
            $this->createMock(TimeContinuumInterface::class)
        );
        $c = new Connection(
            new Http(
                new HttpTranslator($transactions),
                $httpTransport
            ),
            $transactions
        );

        $this->assertFalse($c->isAlive());
    }

    public function testConcrete()
    {
        $q = (new Query\Query)
            ->create('n', ['Foo', 'Bar'])
            ->withProperty('foo', '{bar}')
            ->withParameter('bar', 'baz')
            ->return('n');

        $r = $this->c->execute($q);

        $this->assertSame(1, $r->nodes()->count());
        $this->assertTrue(
            in_array('Bar', $r->nodes()->current()->labels()->toPrimitive())
        );
        $this->assertTrue(
            in_array('Foo', $r->nodes()->current()->labels()->toPrimitive())
        );
        $this->assertCount(1, $r->nodes()->current()->properties());
        $this->assertSame(
            'baz',
            $r->nodes()->current()->properties()->get('foo')
        );
        $this->assertSame(
            'n',
            $r->rows()->current()->column()
        );
        $this->assertSame(
            ['foo' => 'baz'],
            $r->rows()->current()->value()
        );
    }
}
