<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Immutable\{
    MapInterface,
    StreamInterface
};

interface Result
{
    /**
     * Return a list of nodes
     *
     * @return MapInterface<int, NodeInterface>
     */
    public function nodes(): MapInterface;

    /**
     * Return a list of relationships
     *
     * @return MapInterface<int, RelationshipInterface>
     */
    public function relationships(): MapInterface;

    /**
     * Return the rows
     *
     * @return StreamInterface<RowInterface>
     */
    public function rows(): StreamInterface;
}
