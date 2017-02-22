<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Url\UrlInterface;

final class Transaction
{
    private $endpoint;
    private $expiration;
    private $commitEndpoint;

    public function __construct(
        UrlInterface $endpoint,
        PointInTimeInterface $expiration,
        UrlInterface $commitEndpoint
    ) {
        $this->endpoint = $endpoint;
        $this->expiration = $expiration;
        $this->commitEndpoint = $commitEndpoint;
    }

    /**
     * Return the endpoint where to make new queries
     *
     * @return UrlInterface
     */
    public function endpoint(): UrlInterface
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
     * @return UrlInterface
     */
    public function commitEndpoint(): UrlInterface
    {
        return $this->commitEndpoint;
    }
}
