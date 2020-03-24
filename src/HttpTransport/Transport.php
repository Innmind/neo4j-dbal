<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\HttpTransport;

use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\Url\{
    UrlInterface,
    Url,
    Authority\NullUserInformation,
    NullPath,
    NullQuery,
    NullFragment,
};
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
    private UrlInterface $server;
    private Authorization $authorization;
    private TransportInterface $fulfill;

    public function __construct(
        UrlInterface $server,
        TransportInterface $fulfill
    ) {
        $this->server = $server
            ->withAuthority(
                $server->authority()->withUserInformation(new NullUserInformation)
            )
            ->withPath(new NullPath)
            ->withQuery(new NullQuery)
            ->withFragment(new NullFragment);
        $this->authorization = new Authorization(
            new AuthorizationValue(
                'Basic',
                base64_encode(
                    sprintf(
                        '%s:%s',
                        $server->authority()->userInformation()->user(),
                        $server->authority()->userInformation()->password()
                    )
                )
            )
        );
        $this->fulfill = $fulfill;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request): Response
    {
        $request = new Request\Request(
            $this->server->withPath($request->url()->path()),
            $request->method(),
            $request->protocolVersion(),
            $this->addAuthorizationHeader($request->headers()),
            $request->body()
        );

        return ($this->fulfill)($request);
    }

    private function addAuthorizationHeader(Headers $headers): Headers
    {
        return Headers\Headers::of(
            $this->authorization,
            ...\array_values(\iterator_to_array($headers))
        );
    }
}
