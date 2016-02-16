<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\Transactions;
use Innmind\Neo4j\DBAL\Transaction;
use Innmind\Neo4j\DBAL\Server;
use Innmind\Neo4j\DBAL\Authentication;

class TransactionsTest extends \PHPUnit_Framework_TestCase
{
    private $t;
    private $s;

    public function setUp()
    {
        $this->s = new Server(
            'http',
            getenv('CI') ? 'localhost' : 'docker',
            7474
        );
        $auth = new Authentication('neo4j', 'ci');

        $this->t = new Transactions(
            $this->s,
            $auth
        );
    }

    public function testOpen()
    {
        $this->assertFalse($this->t->has());
        $t = $this->t->open();

        $this->assertInstanceOf(Transaction::class, $t);
        $this->assertRegExp(
            '|' . (string) $this->s . 'db/data/transaction/\d|',
            $t->endpoint()
        );
        $this->assertRegExp(
            '|' . (string) $this->s . 'db/data/transaction/\d/commit|',
            $t->commitEndpoint()
        );
        $this->assertTrue($this->t->has());
        $this->assertSame($t, $this->t->get());
    }

    public function testCommit()
    {
        $this->t->open();
        $this->assertSame(
            $this->t,
            $this->t->commit()
        );
        $this->assertFalse($this->t->has());
    }

    /**
     * @expectedException Innmind\Immutable\Exception\OutOfBoundException
     */
    public function testThrowWhenAskingForTransactionWhenThereIsNone()
    {
        $this->t->get();
    }

    /**
     * @expectedException Innmind\Immutable\Exception\OutOfBoundException
     */
    public function testThrowWhenAskingForCommitWhenThereIsNoTransaction()
    {
        $this->t->commit();
    }

    /**
     * @expectedException Innmind\Immutable\Exception\OutOfBoundException
     */
    public function testThrowWhenAskingForRollbackWhenThereIsNoTransaction()
    {
        $this->t->rollback();
    }

    public function testRollback()
    {
        $this->t->open();
        $this->assertSame(
            $this->t,
            $this->t->rollback()
        );
        $this->assertFalse($this->t->has());
    }
}
