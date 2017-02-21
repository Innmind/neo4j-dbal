<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    Transport\Http,
    Translator\HttpTranslator
};
use Innmind\TimeContinuum\{
    TimeContinuumInterface,
    TimeContinuum\Earth
};

final class ConnectionFactory
{
    private $server;
    private $authentication;
    private $clock;

    private function __construct()
    {
    }

    public static function on(string $host, string $scheme = 'https', int $port = 7474): self
    {
        $factory = new self;
        $factory->server = new Server($scheme, $host, $port);

        return $factory;
    }

    public function for(string $user, string $password): self
    {
        $this->authentication = new Authentication($user, $password);

        return $this;
    }

    public function useClock(TimeContinuumInterface $clock): self
    {
        $this->clock = $clock;

        return $this;
    }

    public function build(): ConnectionInterface
    {
        $transactions = new Transactions(
            $this->server,
            $this->authentication,
            $this->clock ?? new Earth
        );

        return new Connection(
            new Http(
                new HttpTranslator($transactions),
                $this->server,
                $this->authentication
            ),
            $transactions
        );
    }
}
