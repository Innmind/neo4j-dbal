<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\TimeContinuum\PointInTime;
use Innmind\Url\Url;

final class Transaction
{
    private Url $endpoint;
    private PointInTime $expiration;
    private Url $commitEndpoint;

    public function __construct(
        Url $endpoint,
        PointInTime $expiration,
        Url $commitEndpoint
    ) {
        $this->endpoint = $endpoint;
        $this->expiration = $expiration;
        $this->commitEndpoint = $commitEndpoint;
    }

    /**
     * Return the endpoint where to make new queries
     *
     * @return Url
     */
    public function endpoint(): Url
    {
        return $this->endpoint;
    }

    /**
     * Return the date at which the transaction will expire
     *
     * @return PointInTime
     */
    public function expiration(): PointInTime
    {
        return $this->expiration;
    }

    /**
     * Return the endpoint to use in order to commit this transaction
     *
     * @return Url
     */
    public function commitEndpoint(): Url
    {
        return $this->commitEndpoint;
    }
}
