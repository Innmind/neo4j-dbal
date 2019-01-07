<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Exception\DomainException;
use Innmind\Immutable\Str;

final class Server
{
    private $scheme;
    private $host;
    private $port;

    public function __construct(
        string $scheme = null,
        string $host = null,
        int $port = null
    ) {
        $scheme = $scheme ?? 'https';
        $host = $host ?? 'localhost';
        $port = $port ?? 7474;

        if (
            Str::of($scheme)->empty() ||
            Str::of($host)->empty() ||
            $port < 1
        ) {
            throw new DomainException;
        }

        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Return the server scheme
     *
     * @return string
     */
    public function scheme(): string
    {
        return $this->scheme;
    }

    /**
     * Return the server host
     *
     * @return string
     */
    public function host(): string
    {
        return $this->host;
    }

    /**
     * Return the server port
     *
     * @return int
     */
    public function port(): int
    {
        return $this->port;
    }

    /**
     * Return the complete uri to the server
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%s://%s:%s/',
            $this->scheme,
            $this->host,
            $this->port
        );
    }
}
