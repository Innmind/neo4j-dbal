<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\HttpTransport\Transport as TransportInterface;
use Innmind\TimeContinuum\Clock;
use Innmind\Url\Url;

function bootstrap(
    TransportInterface $transport,
    Clock $clock,
    Url $server = null
): Connection {
    $httpTransport = new HttpTransport\Transport(
        $server ?? Url::of('https://neo4j:neo4j@localhost:7474/'),
        $transport,
    );
    $transactions = new Transactions($httpTransport, $clock);

    return new Connection\Connection(
        new Transport\Http(
            new Translator\HttpTranslator($transactions),
            $httpTransport,
        ),
        $transactions,
    );
}
