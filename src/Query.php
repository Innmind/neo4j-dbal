<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Immutable\MapInterface;

interface Query
{
    /**
     * Return the cypher query
     *
     * @return string
     */
    public function cypher(): string;

    /**
     * Same as cypher()
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Return the list of parameters
     *
     * @return MapInterface<string, Parameter>
     */
    public function parameters(): MapInterface;

    /**
     * Check if parameters have been set
     *
     * @return bool
     */
    public function hasParameters(): bool;
}
