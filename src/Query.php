<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Query\Parameter;
use Innmind\Immutable\Map;

interface Query
{
    /**
     * Return the cypher query
     */
    public function cypher(): string;

    /**
     * Return the list of parameters
     *
     * @return Map<string, Parameter>
     */
    public function parameters(): Map;

    /**
     * Check if parameters have been set
     */
    public function hasParameters(): bool;
}
