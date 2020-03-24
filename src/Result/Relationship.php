<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Immutable\Map;

interface Relationship
{
    /**
     * Return the relationship id
     *
     * @return Id
     */
    public function id(): Id;

    /**
     * Return the relationship type
     *
     * @return Type
     */
    public function type(): Type;

    /**
     * Return the start node id
     *
     * @return Id
     */
    public function startNode(): Id;

    /**
     * Return the end node id
     *
     * @return Id
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
     *
     * @return bool
     */
    public function hasProperties(): bool;
}
