<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Immutable\{
    Map,
    Sequence,
};

interface Result
{
    /**
     * Return a list of nodes
     *
     * @return Map<int, Result\Node>
     */
    public function nodes(): Map;

    /**
     * Return a list of relationships
     *
     * @return Map<int, Result\Relationship>
     */
    public function relationships(): Map;

    /**
     * Return the rows
     *
     * @return Sequence<Result\Row>
     */
    public function rows(): Sequence;
}
