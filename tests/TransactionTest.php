<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Transaction;
use Innmind\TimeContinuum\PointInTimeInterface;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testInterface()
    {
        $t = new Transaction(
            'http://localhost:7474/db/data/transaction/9',
            $expiration = $this->createMock(PointInTimeInterface::class),
            'http://localhost:7474/db/data/transaction/9/commit'
        );

        $this->assertSame(
            'http://localhost:7474/db/data/transaction/9',
            $t->endpoint()
        );
        $this->assertSame($expiration, $t->expiration());
        $this->assertSame(
            'http://localhost:7474/db/data/transaction/9/commit',
            $t->commitEndpoint()
        );
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyEndpoint()
    {
        new Transaction(
            '',
            $this->createMock(PointInTimeInterface::class),
            'somewhere'
        );
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyCommitEndpoint()
    {
        new Transaction(
            'somewhere',
            $this->createMock(PointInTimeInterface::class),
            ''
        );
    }
}
