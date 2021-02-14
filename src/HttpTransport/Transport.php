<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\HttpTransport;

use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\Url\Url;
use Innmind\Http\{
    Header,
    Header\Authorization,
    Header\AuthorizationValue,
    Message\Request,
    Message\Response,
    Headers,
};
use Innmind\Immutable\Map;

final class Transport implements TransportInterface
{
    private Url $server;
    private Authorization $authorization;
    private TransportInterface $fulfill;

    public function __construct(
        Url $server,
        TransportInterface $fulfill
    ) {
        $this->server = $server
            ->withAuthority($server->authority()->withoutUserInformation())
            ->withoutPath()
            ->withoutQuery()
            ->withoutFragment();
        $this->authorization = Authorization::of(
            'Basic',
            \base64_encode(
                \sprintf(
                    '%s:%s',
                    $server->authority()->userInformation()->user()->toString(),
                    $server->authority()->userInformation()->password()->toString(),
                ),
            ),
        );
        $this->fulfill = $fulfill;
    }

    public function __invoke(Request $request): Response
    {
        /** @psalm-suppress InvalidArgument */
        $request = new Request\Request(
            $this->server->withPath($request->url()->path()),
            $request->method(),
            $request->protocolVersion(),
            $request->headers()->add($this->authorization),
            $request->body(),
        );

        return ($this->fulfill)($request);
    }
}
