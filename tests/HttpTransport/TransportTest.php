<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\HttpTransport;

use Innmind\Neo4j\DBAL\{
    HttpTransport\Transport,
};
use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\Http\{
    Message\Request,
    Message\Response,
    Message\Method,
    ProtocolVersion,
    Headers,
    Header,
    Header\ContentType,
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
                Url::of('http://neo4j:ci@localhost:7474/'),
                $this->createMock(TransportInterface::class)
            )
        );
    }

    public function testFulfill()
    {
        $baseRequest = new Request\Request(
            Url::of('http://localhost:7474/path'),
            new Method('POST'),
            new ProtocolVersion(1, 1),
            Headers::of(
                ContentType::of('application', 'json'),
            )
        );
        $mock = $this->createMock(TransportInterface::class);
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function(Request $request) use ($baseRequest): bool {
                return $request->url()->toString() === 'https://somewhere:7473/path' &&
                    $request->method() === $baseRequest->method() &&
                    $request->protocolVersion() === $baseRequest->protocolVersion() &&
                    $request->body() === $baseRequest->body() &&
                    $request->headers()->count() === 2 &&
                    $request->headers()->contains('authorization') &&
                    $request->headers()->get('authorization')->toString() === 'Authorization: "Basic" dXNlcjpwd2Q=' &&
                    $request->headers()->contains('content-type') &&
                    $request->headers()->get('content-type') === $baseRequest->headers()->get('content-type');
            }))
            ->willReturn(
                $expected = $this->createMock(Response::class)
            );
        $fulfill = new Transport(
            Url::of('https://user:pwd@somewhere:7473/'),
            $mock
        );

        $this->assertSame($expected, $fulfill($baseRequest));
    }
}
