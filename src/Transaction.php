<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

class Transaction
{
    private $endpoint;
    private $expiration;
    private $commitEndpoint;

    public function __construct(
        string $endpoint,
        string $expiration,
        string $commitEndpoint
    ) {
        $this->endpoint = $endpoint;
        $this->expiration = new \DateTimeImmutable($expiration);
        $this->commitEndpoint = $commitEndpoint;
    }

    /**
     * Return the endpoint where to make new queries
     *
     * @return string
     */
    public function endpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Return the date at which the transaction will expire
     *
     * @return DateTimeInterface
     */
    public function expiration(): \DateTimeInterface
    {
        return $this->expiration;
    }

    /**
     * Return the endpoint to use in order to commit this transaction
     *
     * @return string
     */
    public function commitEndpoint(): string
    {
        return $this->commitEndpoint;
    }
}