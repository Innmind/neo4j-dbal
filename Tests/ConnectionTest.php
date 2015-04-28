<?php

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     */
    public function testInvalidSchemeType()
    {
        new Connection(
            ['scheme' => null],
            new EventDispatcher()
        );
    }

    /**
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testInvalidSchemeValue()
    {
        new Connection(
            ['scheme' => 'ftp'],
            new EventDispatcher()
        );
    }

    public function testApiBaseUrl()
    {
        $conn = new Connection(
            [],
            new EventDispatcher()
        );

        $refl = new \ReflectionObject($conn);
        $http = $refl->getProperty('http');
        $http->setAccessible(true);
        $http = $http->getValue($conn);
        $refl = new \ReflectionObject($http);
        $base = $refl->getProperty('baseUrl');
        $base->setAccessible(true);

        $this->assertEquals(
            'http://localhost:7474/db/data/',
            (string) $base->getValue($http)
        );
    }

    public function testApiCredentials()
    {
        $conn = new Connection(
            ['username' => 'foo', 'password' => 'bar'],
            new EventDispatcher()
        );

        $refl = new \ReflectionObject($conn);
        $http = $refl->getProperty('http');
        $http->setAccessible(true);
        $http = $http->getValue($conn);
        $refl = new \ReflectionObject($http);
        $defaults = $refl->getProperty('defaults');
        $defaults->setAccessible(true);

        $this->assertEquals(
            'Basic Zm9vOmJhcg==',
            $defaults->getValue($http)['headers']['Authorization']
        );
    }

    public function testGetDispatcher()
    {
        $d = new EventDispatcher;
        $conn = new Connection([], $d);

        $this->assertEquals($d, $conn->getDispatcher());
    }
}
