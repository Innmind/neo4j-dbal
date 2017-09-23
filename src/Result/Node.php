<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Immutable\{
    SetInterface,
    MapInterface
};

interface Node
{
    /**
     * Return the node id
     *
     * @return Id
     */
    public function id(): Id;

    /**
     * Return the labels
     *
     * @return SetInterface<string>
     */
    public function labels(): SetInterface;

    /**
     * Check if the node has labels
     *
     * @return bool
     */
    public function hasLabels(): bool;

    /**
     * Return the properties
     *
     * @return MapInterface<string, variable>
     */
    public function properties(): MapInterface;

    /**
     * Check if the node has properties
     *
     * @return bool
     */
    public function hasProperties(): bool;
}
