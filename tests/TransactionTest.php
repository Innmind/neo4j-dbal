<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testInterface()
    {
        $t = new Transaction(
            'http://localhost:7474/db/data/transaction/9',
            'Wed, 13 Jan 2016 12:12:42 +0200',
            'http://localhost:7474/db/data/transaction/9/commit'
        );

        $this->assertSame(
            'http://localhost:7474/db/data/transaction/9',
            $t->endpoint()
        );
        $this->assertInstanceOf(\DateTimeInterface::class, $t->expiration());
        $this->assertSame(
            '2016-01-13T12:12:42+02:00',
            $t->expiration()->format('c')
        );
        $this->assertSame(
            'http://localhost:7474/db/data/transaction/9/commit',
            $t->commitEndpoint()
        );
    }
}
