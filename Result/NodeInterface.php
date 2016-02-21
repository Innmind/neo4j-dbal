<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Immutable\CollectionInterface;

interface NodeInterface
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
     * @return CollectionInterface
     */
    public function labels(): CollectionInterface;

    /**
     * Check if the node has labels
     *
     * @return bool
     */
    public function hasLabels(): bool;

    /**
     * Return the properties
     *
     * @return CollectionInterface
     */
    public function properties(): CollectionInterface;

    /**
     * Check if the node has properties
     *
     * @return bool
     */
    public function hasProperties(): bool;
}
