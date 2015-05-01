<?php

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\DelegateConnection;
use Innmind\Neo4j\DBAL\ConnectionFactory;

class DelegateConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddConnection()
    {
        $conn = new DelegateConnection;

        $this->assertEquals($conn, $conn->addConnection('foo', ConnectionFactory::make()));
        $this->assertInstanceOf('Innmind\\Neo4j\\DBAL\\Connection', $conn->getConnection('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsWhenGettingUnknownConnection()
    {
        $conn = new DelegateConnection;

        $conn->getConnection('foo');
    }

    public function testgetActiveConnection()
    {
        $conn = new DelegateConnection;
        $mock = $this->getConnMock();
        $mock
            ->method('isAlive')
            ->willReturn(true);
        $conn->addConnection('foo', $mock);

        $this->assertEquals($mock, $conn->getActiveConnection());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsWhenNoConnectionALive()
    {
        $conn = new DelegateConnection;

        $conn->getActiveConnection();
    }

    public function testDelegateOneMethod()
    {
        $conn = new DelegateConnection;
        $mock = $this->getConnMock();
        $mock
            ->expects($this->at(0))
            ->method('isAlive')
            ->will($this->returnValue(true));
        $mock
            ->expects($this->at(1))
            ->method('isAlive')
            ->will($this->returnValue(true));
        $mock
            ->expects($this->at(2))
            ->method('isAlive')
            ->will($this->returnValue(false));

        $conn->addConnection('foo', $mock);

        $this->assertTrue($conn->isAlive());
        $this->assertFalse($conn->isAlive());
    }

    protected function getConnMock()
    {
        $mock = $this
            ->getMockBuilder('Innmind\\Neo4j\\DBAL\\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }
}
