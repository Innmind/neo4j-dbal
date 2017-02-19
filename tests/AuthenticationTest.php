<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Authentication;

class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $a = new Authentication('neo4j', 'docker');

        $this->assertSame('neo4j', $a->user());
        $this->assertSame('docker', $a->password());
    }
}