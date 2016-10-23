<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Result\Id;

class IdTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $i = new Id(42);

        $this->assertSame(42, $i->value());
        $this->assertSame('42', (string) $i);
    }
}
