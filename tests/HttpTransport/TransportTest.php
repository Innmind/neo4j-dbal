<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\HttpTransport;

use Innmind\Neo4j\DBAL\{
    HttpTransport\Transport,
    Server,
    Authentication
};
use Innmind\HttpTransport\TransportInterface;
use Innmind\Http\{
    Message\RequestInterface,
    Message\Request,
    Message\ResponseInterface,
    Message\Method,
    ProtocolVersion,
    Headers,
    Header\HeaderInterface,
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
        $baseRequest = new Request(
            Url::fromString('http://localhost:7474/path'),
            new Method('POST'),
            new ProtocolVersion(1, 1),
            new Headers(
                (new Map('string', HeaderInterface::class))
                    ->put(
                        'content-type',
                        new ContentType(
                            new ContentTypeValue('application', 'json')
                        )
                    )
            )
        );
        $mock = $this->createMock(TransportInterface::class);
        $mock
            ->expects($this->once())
            ->method('fulfill')
            ->with($this->callback(function(RequestInterface $request) use ($baseRequest): bool {
                return (string) $request->url() === 'https://somewhere:7473/path' &&
                    $request->method() === $baseRequest->method() &&
                    $request->protocolVersion() === $baseRequest->protocolVersion() &&
                    $request->body() === $baseRequest->body() &&
                    $request->headers()->count() === 2 &&
                    $request->headers()->has('authorization') &&
                    (string) $request->headers()->get('authorization') === 'Authorization : "Basic" dXNlcjpwd2Q=' &&
                    $request->headers()->has('content-type') &&
                    $request->headers()->get('content-type') === $baseRequest->headers()->get('content-type');
            }))
            ->willReturn(
                $expected = $this->createMock(ResponseInterface::class)
            );
        $transport = new Transport(
            new Server('https', 'somewhere', 7473),
            new Authentication('user', 'pwd'),
            $mock
        );

        $this->assertSame($expected, $transport->fulfill($baseRequest));
    }
}
