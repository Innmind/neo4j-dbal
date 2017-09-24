<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Authentication;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{
    public function testGetters()
    {
        $a = new Authentication('neo4j', 'docker');

        $this->assertSame('neo4j', $a->user());
        $this->assertSame('docker', $a->password());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyUser()
    {
        new Authentication('', 'ci');
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyPassword()
    {
        new Authentication('foo', '');
    }
}
