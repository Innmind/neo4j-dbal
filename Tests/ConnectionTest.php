<?php

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\Connection;
use Innmind\Neo4j\DBAL\CypherBuilder;
use Innmind\Neo4j\DBAL\Query;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    protected $host = 'docker';
    protected $password;

    public function setUp()
    {
        if (getenv('CI')) {
            $this->host = 'localhost';
            $this->password = 'ci';
        }
    }

    /**
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     */
    public function testInvalidSchemeType()
    {
        new Connection(
            ['scheme' => null],
            new EventDispatcher,
            new CypherBuilder
        );
    }

    /**
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testInvalidSchemeValue()
    {
        new Connection(
            ['scheme' => 'ftp'],
            new EventDispatcher,
            new CypherBuilder
        );
    }

    public function testApiBaseUrl()
    {
        $conn = new Connection(
            [],
            new EventDispatcher,
            new CypherBuilder
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
            new EventDispatcher,
            new CypherBuilder
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
        $conn = new Connection([], $d, new CypherBuilder);

        $this->assertEquals($d, $conn->getDispatcher());
    }

    public function testExecuteQuery()
    {
        $p = ['host' => $this->host];
        if ($this->password) {
            $p['username'] = 'neo4j';
            $p['password'] = $this->password;
        }

        $conn = new Connection(
            $p,
            new EventDispatcher,
            new CypherBuilder
        );
        $q = new Query;
        $q
            ->create('(a:Foo:Bar {props})')
            ->addParameter('props', [
                'name' => 'foo'
            ])
            ->setReturn('a');

        $response = $conn->executeQuery($q);

        $this->assertEquals(1, count($response['nodes']));
        $this->assertEquals(1, count($response['results']));
        $this->assertEquals(
            ['Foo', 'Bar'],
            $response['nodes'][0]['labels']
        );
        $this->assertEquals(
            ['name' => 'foo'],
            $response['nodes'][0]['properties']
        );
    }

    public function testExecute()
    {
        $p = ['host' => $this->host];
        if ($this->password) {
            $p['username'] = 'neo4j';
            $p['password'] = $this->password;
        }

        $conn = new Connection(
            $p,
            new EventDispatcher,
            new CypherBuilder
        );

        $response = $conn->execute(
            'CREATE (a:Baz {props})-[r:Test]->(b:Baz {props}) RETURN a, b, r',
            ['props' => ['name' => 'baz']]
        );

        $this->assertEquals(2, count($response['nodes']));
        $this->assertEquals(1, count($response['relationships']));
        $this->assertEquals(1, count($response['results']));
        $this->assertEquals(
            ['Baz'],
            $response['nodes'][0]['labels']
        );
        $this->assertEquals(
            ['name' => 'baz'],
            $response['nodes'][0]['properties']
        );
        $this->assertEquals(
            'Test',
            $response['relationships'][0]['type']
        );
    }

    public function testExecuteQueries()
    {
        $p = ['host' => $this->host];
        if ($this->password) {
            $p['username'] = 'neo4j';
            $p['password'] = $this->password;
        }

        $conn = new Connection(
            $p,
            new EventDispatcher,
            new CypherBuilder
        );
        $q = new Query;
        $q
            ->create('(a {props})')
            ->addParameter('props', ['name' => 'foo'])
            ->setReturn('a');

        $response = $conn->executeQueries([
            $q,
            [
                'query' => 'CREATE (b {props}) RETURN b',
                'parameters' => ['props' => ['name' => 'bar']],
            ]
        ]);

        $this->assertEquals(2, count($response['nodes']));
        $this->assertEquals(2, count($response['results']));
    }
}
