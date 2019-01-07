<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Url\{
    UrlInterface,
    Url,
};

function bootstrap(
    TransportInterface $transport,
    TimeContinuumInterface $clock,
    UrlInterface $server = null
): Connection {
    $httpTransport = new HttpTransport\Transport(
        $server ?? Url::fromString('https://neo4j:neo4j@localhost:7474/'),
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
