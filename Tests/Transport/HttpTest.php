<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Transport;

use Innmind\Neo4j\DBAL\{
    Transport\Http,
    Translator\HttpTranslator,
    Server,
    Authentication,
    Transactions,
    QueryInterface,
    ResultInterface,
    Events,
    Event\PreQueryEvent,
    Event\PostQueryEvent
};
use Symfony\Component\EventDispatcher\EventDispatcher;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    private $t;
    private $d;

    public function setUp()
    {
        $server = new Server(
            'http',
            getenv('CI') ? 'localhost' : 'docker',
            7474
        );
        $auth = new Authentication('neo4j', 'ci');
        $this->t = new Http(
            new HttpTranslator(
                new Transactions($server, $auth)
            ),
            $this->d = new EventDispatcher,
            $server,
            $auth
        );
    }

    public function testPing()
    {
        $this->assertSame($this->t, $this->t->ping());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\ServerDownException
     */
    public function testThrowWhenPingUnavailableServer()
    {
        $server = new Server(
            'http',
            'localhost',
            1337
        );
        $auth = new Authentication('neo4j', 'ci');
        $t = new Http(
            new HttpTranslator(
                new Transactions($server, $auth)
            ),
            new EventDispatcher,
            $server,
            $auth
        );

        $t->ping();
    }

    public function testExecute()
    {
        $preFired = $postFired = false;
        $this->d->addListener(
            Events::PRE_QUERY,
            function (PreQueryEvent $event) use (&$preFired) {
                $preFired = true;
            }
        );
        $this->d->addListener(
            Events::POST_QUERY,
            function (PostQueryEvent $event) use (&$postFired) {
                $postFired = true;
            }
        );
        $q = $this->getMock(QueryInterface::class);
        $q
            ->method('cypher')
            ->willReturn('match n return n;');

        $r = $this->t->execute($q);

        $this->assertInstanceOf(ResultInterface::class, $r);
        $this->assertTrue($preFired);
        $this->assertTrue($postFired);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\QueryException
     * @expectedExceptionMessage The query failed to execute properly
     * @expectedExceptionCode 400
     */
    public function testThrowWhenQueryFailed()
    {
        $q = $this->getMock(QueryInterface::class);
        $q
            ->method('cypher')
            ->willReturn('foo');

        $this->t->execute($q);
    }

    public function testDispatcher()
    {
        $this->assertSame($this->d, $this->t->dispatcher());
    }
}
