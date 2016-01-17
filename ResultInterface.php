<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

interface ResultInterface
{
    /**
     * Return a list of nodes
     *
     * @return TypedCollectionInterface
     */
    public function getNodes(): TypedCollectionInterface;

    /**
     * Return a list of relationships
     *
     * @return TypedCollectionInterface
     */
    public function getRelationships(): TypedCollectionInterface;

    /**
     * Return the rows
     *
     * @return TypedCollectionInterface
     */
    public function getRows(): TypedCollectionInterface;
}
