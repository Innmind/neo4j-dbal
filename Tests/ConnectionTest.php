<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\{
    Connection,
    ConnectionInterface,
    Server,
    Authentication,
    Transactions,
    Transport\Http,
    QueryInterface,
    ResultInterface,
    Translator\HttpTranslator
};
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    private $c;

    public function setUp()
    {
        $server = new Server(
            'http',
            getenv('CI') ? 'localhost' : 'docker',
            7474
        );
        $auth = new Authentication('neo4j', 'ci');
        $transactions = new Transactions($server, $auth);
        $this->c = new Connection(
            new Http(
                new HttpTranslator($transactions),
                new EventDispatcher,
                $server,
                $auth
            ),
            $transactions
        );
    }

    public function testExecute()
    {
        $q = $this->getMock(QueryInterface::class);
        $q
            ->method('cypher')
            ->willReturn('match n return n');

        $r = $this->c->execute($q);

        $this->assertInstanceOf(ResultInterface::class, $r);
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
        $transactions = new Transactions($server, $auth);
        $c = new Connection(
            new Http(
                new HttpTranslator($transactions),
                new EventDispatcher,
                $server,
                $auth
            ),
            $transactions
        );

        $this->assertFalse($c->isAlive());
    }
}
