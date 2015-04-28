<?php

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\ConnectionFactory;
use Innmind\Neo4j\DBAL\Events;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDefaultEventDispatcher()
    {
        $conn = ConnectionFactory::make();

        $this->assertTrue($conn->getDispatcher() instanceof EventDispatcher);
    }

    public function testSetDefaultEventListener()
    {
        $conn = ConnectionFactory::make();

        $this->assertTrue($conn->getDispatcher()->hasListeners(Events::API_RESPONSE));
        $this->assertEquals(1, count($conn->getDispatcher()->getListeners(Events::API_RESPONSE)));
    }

    public function testDontSetListenerForImmutableDispatcher()
    {
        $conn = ConnectionFactory::make([], new ImmutableEventDispatcher(new EventDispatcher));

        $this->assertFalse($conn->getDispatcher()->hasListeners(Events::API_RESPONSE));
    }
}
