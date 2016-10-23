<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Translator;

use Innmind\Neo4j\DBAL\Translator\HttpTranslator;
use Innmind\Neo4j\DBAL\Transactions;
use Innmind\Neo4j\DBAL\Server;
use Innmind\Neo4j\DBAL\Authentication;
use Innmind\Neo4j\DBAL\QueryInterface;
use Innmind\Neo4j\DBAL\Query\Parameter;
use Innmind\Immutable\TypedCollection;
use Psr\Http\Message\RequestInterface;

class HttpTranslatorTest extends \PHPUnit_Framework_TestCase
{
    private $t;

    public function setUp()
    {
        $this->t = new HttpTranslator(
            new Transactions(
                new Server(
                    'http',
                    getenv('CI') ? 'localhost' : 'docker',
                    7474
                ),
                new Authentication(
                    'neo4j',
                    'ci'
                )
            )
        );
    }

    public function testTranslate()
    {
        $q = $this->createMock(QueryInterface::class);
        $q
            ->method('cypher')
            ->willReturn('match n return n;');
        $q
            ->method('hasParameters')
            ->willReturn(true);
        $q
            ->method('parameters')
            ->willReturn(new TypedCollection(
                Parameter::class,
                [new Parameter('foo', 'bar')]
            ));

        $r = $this->t->translate($q);

        $this->assertInstanceOf(RequestInterface::class, $r);
        $this->assertSame('POST', $r->getMethod());
        $this->assertSame('/db/data/transaction/commit', $r->getRequestTarget());
        $this->assertSame(
            json_encode([
                'statements' => [[
                    'statement' => 'match n return n;',
                    'resultDataContents' => ['graph', 'row'],
                    'parameters' => ['foo' => 'bar'],
                ]],
            ]),
            (string) $r->getBody()
        );
    }
}
