<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Translator;

use Innmind\Neo4j\DBAL\{
    Translator\HttpTranslator,
    Transactions,
    Server,
    Authentication,
    QueryInterface,
    Query\Parameter
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Immutable\Map;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase;

class HttpTranslatorTest extends TestCase
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
                ),
                $this->createMock(TimeContinuumInterface::class)
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
            ->willReturn(
                (new Map('string', Parameter::class))
                    ->put('foo', new Parameter('foo', 'bar'))
            );

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
