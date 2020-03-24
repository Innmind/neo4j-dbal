<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Immutable\{
    Set,
    Map,
};

interface Node
{
    /**
     * Return the node id
     */
    public function id(): Id;

    /**
     * Return the labels
     *
     * @return Set<string>
     */
    public function labels(): Set;

    /**
     * Check if the node has labels
     */
    public function hasLabels(): bool;

    /**
     * Return the properties
     *
     * @return Map<string, scalar|array>
     */
    public function properties(): Map;

    /**
     * Check if the node has properties
     */
    public function hasProperties(): bool;
}
