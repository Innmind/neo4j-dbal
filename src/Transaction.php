<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Exception\InvalidArgumentException;
use Innmind\TimeContinuum\PointInTimeInterface;

final class Transaction
{
    private $endpoint;
    private $expiration;
    private $commitEndpoint;

    public function __construct(
        string $endpoint,
        PointInTimeInterface $expiration,
        string $commitEndpoint
    ) {
        if (empty($endpoint) || empty($commitEndpoint)) {
            throw new InvalidArgumentException;
        }

        $this->endpoint = $endpoint;
        $this->expiration = $expiration;
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
     * @return PointInTimeInterface
     */
    public function expiration(): PointInTimeInterface
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
