<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\TimeContinuum\TimeContinuumInterface;

function bootstrap(
    TransportInterface $transport,
    TimeContinuumInterface $clock,
    string $scheme = null,
    string $host = null,
    int $port = null,
    string $user = null,
    string $password = null
): Connection {
    $httpTransport = new HttpTransport\Transport(
        new Server($scheme, $host, $port),
        new Authentication($user, $password),
        $transport
    );
    $transactions = new Transactions($httpTransport, $clock);

    return new Connection\Connection(
        new Transport\Http(
            new Translator\HttpTranslator($transactions),
            $httpTransport
        ),
        $transactions
    );
}
