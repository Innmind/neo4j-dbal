<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Immutable\TypedCollectionInterface;

interface QueryInterface
{
    /**
     * Return the cypher query
     *
     * @return string
     */
    public function cypher(): string;

    /**
     * Same as getCypher
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Return the list of parameters
     *
     * @return TypedCollection
     */
    public function parameters(): TypedCollectionInterface;

    /**
     * Check if parameters have been set
     *
     * @return bool
     */
    public function hasParameters(): bool;
}
