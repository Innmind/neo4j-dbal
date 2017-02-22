<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\HttpTransport;

use Innmind\Neo4j\DBAL\{
    Server,
    Authentication
};
use Innmind\HttpTransport\TransportInterface;
use Innmind\Url\{
    Url,
    Scheme,
    Authority,
    Authority\NullUserInformation,
    Authority\Host,
    Authority\Port,
    NullPath,
    NullQuery,
    NullFragment
};
use Innmind\Http\{
    Header\HeaderInterface,
    Header\Authorization,
    Header\AuthorizationValue,
    Message\RequestInterface,
    Message\Request,
    Message\ResponseInterface,
    HeadersInterface,
    Headers
};
use Innmind\Immutable\Map;

final class Transport implements TransportInterface
{
    private $server;
    private $authorization;
    private $transport;

    public function __construct(
        Server $server,
        Authentication $authentication,
        TransportInterface $transport
    ) {
        $this->server = new Url(
            new Scheme($server->scheme()),
            new Authority(
                new NullUserInformation,
                new Host($server->host()),
                new Port($server->port())
            ),
            new NullPath,
            new NullQuery,
            new NullFragment
        );
        $this->authorization = new Authorization(
            new AuthorizationValue(
                'Basic',
                base64_encode(
                    sprintf(
                        '%s:%s',
                        $authentication->user(),
                        $authentication->password()
                    )
                )
            )
        );
        $this->transport = $transport;
    }

    /**
     * {@inheritdoc}
     */
    public function fulfill(RequestInterface $request): ResponseInterface
    {
        $request = new Request(
            $this->server->withPath($request->url()->path()),
            $request->method(),
            $request->protocolVersion(),
            $this->addAuthorizationHeader($request->headers()),
            $request->body()
        );

        return $this->transport->fulfill($request);
    }

    private function addAuthorizationHeader(HeadersInterface $headers): Headers
    {
        $map = new Map('string', HeaderInterface::class);

        foreach ($headers as $header) {
            $map = $map->put($header->name(), $header);
        }

        $map = $map->put($this->authorization->name(), $this->authorization);

        return new Headers($map);
    }
}
