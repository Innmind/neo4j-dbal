<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Translator;

use Innmind\Neo4j\DBAL\{
    Translator\HttpTranslator,
    Transactions,
    Query,
    Query\Parameter,
    HttpTransport\Transport,
};
use Innmind\TimeContinuum\Clock;
use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\Http\{
    Message\Request,
    Message\Response,
    Headers,
    Header\Header,
    Header\Value\Value,
};
use Innmind\Url\Url;
use Innmind\Stream\Readable;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class HttpTranslatorTest extends TestCase
{
    private $translate;
    private $transactions;
    private $transport;

    public function setUp(): void
    {
        $this->translate = new HttpTranslator(
            $this->transactions = new Transactions(
                new Transport(
                    Url::of('http://neo4j:ci@localhost:7474/'),
                    $this->transport = $this->createMock(TransportInterface::class)
                ),
                $this->createMock(Clock::class)
            )
        );
    }

    public function testTranslate()
    {
        $query = $this->createMock(Query::class);
        $query
            ->method('cypher')
            ->willReturn('match n return n;');
        $query
            ->method('hasParameters')
            ->willReturn(true);
        $query
            ->method('parameters')
            ->willReturn(
                Map::of('string', Parameter::class)
                    ('foo', new Parameter('foo', 'bar'))
            );

        $request = ($this->translate)($query);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame('POST', $request->method()->toString());
        $this->assertSame('/db/data/transaction/commit', $request->url()->toString());
        $this->assertSame(
            json_encode([
                'statements' => [[
                    'statement' => 'match n return n;',
                    'resultDataContents' => ['graph', 'row'],
                    'parameters' => ['foo' => 'bar'],
                ]],
            ]),
            $request->body()->toString(),
        );
    }

    public function testTranslateWithOpenedTransaction()
    {
        $query = $this->createMock(Query::class);
        $query
            ->method('cypher')
            ->willReturn('match n return n;');
        $query
            ->method('hasParameters')
            ->willReturn(true);
        $query
            ->method('parameters')
            ->willReturn(
                Map::of('string', Parameter::class)
                    ('foo', new Parameter('foo', 'bar'))
            );
        $this
            ->transport
            ->expects($this->at(0))
            ->method('__invoke')
            ->willReturn($response = $this->createMock(Response::class));
        $response
            ->expects($this->once())
            ->method('body')
            ->willReturn($body = $this->createMock(Readable::class));
        $body
            ->expects($this->once())
            ->method('toString')
            ->willReturn('{"transaction":{"expires":"+1hour"},"commit":"/db/data/transaction/1/commit"}');
        $response
            ->expects($this->once())
            ->method('headers')
            ->willReturn(Headers::of(
                new Header('Location', new Value('/db/data/transaction/1'))
            ));

        $this->transactions->open();
        $request = ($this->translate)($query);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame('POST', $request->method()->toString());
        $this->assertSame('/db/data/transaction/1', $request->url()->toString());
        $this->assertSame(
            json_encode([
                'statements' => [[
                    'statement' => 'match n return n;',
                    'resultDataContents' => ['graph', 'row'],
                    'parameters' => ['foo' => 'bar'],
                ]],
            ]),
            $request->body()->toString()
        );
    }
}
