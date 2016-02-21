<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

interface PathAwareInterface extends ParametrableInterface
{
    /**
     * Link the currently matched node to another node
     *
     * @param string $variable
     * @param array $labels
     *
     * @return CauseInterface
     */
    public function linkedTo(
        string $variable = null,
        array $labels = []
    ): ClauseInterface;

    /**
     * Type the last connection
     *
     * @param string $variable
     * @param string $type
     * @param string $direction
     *
     * @return ClauseInterface
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = Expression\Relationship::BOTH
    ): ClauseInterface;

    /**
     * Specify a property to be matched
     *
     * @param string $property
     * @param string $cypher
     *
     * @return ClauseInterface
     */
    public function withProperty(
        string $property,
        string $cypher
    ): ClauseInterface;
}
