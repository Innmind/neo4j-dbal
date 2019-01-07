<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\HttpTransport;

use Innmind\Neo4j\DBAL\{
    HttpTransport\Transport,
    Server,
    Authentication
};
use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\Http\{
    Message\Request,
    Message\Response,
    Message\Method\Method,
    ProtocolVersion\ProtocolVersion,
    Headers,
    Header,
    Header\ContentType,
    Header\ContentTypeValue
};
use Innmind\Url\Url;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class TransportTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            TransportInterface::class,
            new Transport(
                new Server('foo', 'bar', 7474),
                new Authentication('user', 'pwd'),
                $this->createMock(TransportInterface::class)
            )
        );
    }

    public function testFulfill()
    {
        $baseRequest = new Request\Request(
            Url::fromString('http://localhost:7474/path'),
            new Method('POST'),
            new ProtocolVersion(1, 1),
            Headers\Headers::of(
                new ContentType(
                    new ContentTypeValue('application', 'json')
                )
            )
        );
        $mock = $this->createMock(TransportInterface::class);
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function(Request $request) use ($baseRequest): bool {
                return (string) $request->url() === 'https://somewhere:7473/path' &&
                    $request->method() === $baseRequest->method() &&
                    $request->protocolVersion() === $baseRequest->protocolVersion() &&
                    $request->body() === $baseRequest->body() &&
                    $request->headers()->count() === 2 &&
                    $request->headers()->has('authorization') &&
                    (string) $request->headers()->get('authorization') === 'Authorization: "Basic" dXNlcjpwd2Q=' &&
                    $request->headers()->has('content-type') &&
                    $request->headers()->get('content-type') === $baseRequest->headers()->get('content-type');
            }))
            ->willReturn(
                $expected = $this->createMock(Response::class)
            );
        $fulfill = new Transport(
            new Server('https', 'somewhere', 7473),
            new Authentication('user', 'pwd'),
            $mock
        );

        $this->assertSame($expected, $fulfill($baseRequest));
    }
}
