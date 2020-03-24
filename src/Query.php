<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Immutable\Map;

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
     * @return Map<string, Parameter>
     */
    public function parameters(): Map;

    /**
     * Check if parameters have been set
     *
     * @return bool
     */
    public function hasParameters(): bool;
}
