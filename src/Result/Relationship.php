<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Immutable\Map;

interface Relationship
{
    /**
     * Return the relationship id
     */
    public function id(): Id;

    /**
     * Return the relationship type
     */
    public function type(): Type;

    /**
     * Return the start node id
     */
    public function startNode(): Id;

    /**
     * Return the end node id
     */
    public function endNode(): Id;

    /**
     * Return the properties
     *
     * @return Map<string, scalar|array>
     */
    public function properties(): Map;

    /**
     * Check if the relationship has properties
     */
    public function hasProperties(): bool;
}
