<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Translator;

use Innmind\Neo4j\DBAL\{
    Translator\HttpTranslator,
    Transactions,
    Server,
    Authentication,
    QueryInterface,
    Query\Parameter,
    HttpTransport\Transport
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\Http\Message\Request;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class HttpTranslatorTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        $this->translator = new HttpTranslator(
            new Transactions(
                new Transport(
                    new Server(
                        'http',
                        getenv('CI') ? 'localhost' : 'docker',
                        7474
                    ),
                    new Authentication(
                        'neo4j',
                        'ci'
                    ),
                    $this->createMock(TransportInterface::class)
                ),
                $this->createMock(TimeContinuumInterface::class)
            )
        );
    }

    public function testTranslate()
    {
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('cypher')
            ->willReturn('match n return n;');
        $query
            ->method('hasParameters')
            ->willReturn(true);
        $query
            ->method('parameters')
            ->willReturn(
                (new Map('string', Parameter::class))
                    ->put('foo', new Parameter('foo', 'bar'))
            );

        $request = $this->translator->translate($query);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame('POST', (string) $request->method());
        $this->assertSame('/db/data/transaction/commit', (string) $request->url());
        $this->assertSame(
            json_encode([
                'statements' => [[
                    'statement' => 'match n return n;',
                    'resultDataContents' => ['graph', 'row'],
                    'parameters' => ['foo' => 'bar'],
                ]],
            ]),
            (string) $request->body()
        );
    }
}
