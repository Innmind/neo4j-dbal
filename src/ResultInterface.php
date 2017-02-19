<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Immutable\TypedCollectionInterface;

interface ResultInterface
{
    /**
     * Return a list of nodes
     *
     * @return TypedCollectionInterface
     */
    public function nodes(): TypedCollectionInterface;

    /**
     * Return a list of relationships
     *
     * @return TypedCollectionInterface
     */
    public function relationships(): TypedCollectionInterface;

    /**
     * Return the rows
     *
     * @return TypedCollectionInterface
     */
    public function rows(): TypedCollectionInterface;
}