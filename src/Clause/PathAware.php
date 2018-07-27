<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause;

interface PathAware extends Parametrable
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
    ): Clause;

    /**
     * Type the last connection
     *
     * @param string $variable
     * @param string $type
     * @param string $direction
     *
     * @return Clause
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = Expression\Relationship::BOTH
    ): Clause;

    /**
     * Specify a property to be matched
     *
     * @param string $property
     * @param string $cypher
     *
     * @return Clause
     */
    public function withProperty(
        string $property,
        string $cypher
    ): Clause;

    /**
     * Define the deepness of the relationship
     *
     * @param int $distance
     *
     * @throws LogicException If no relationship in the path
     *
     * @return Clause
     */
    public function withADistanceOf(int $distance): Clause;

    /**
     * Define the deepness range of the relationship
     *
     * @param int $min
     * @param int $max
     *
     * @throws LogicException If no relationship in the path
     *
     * @return Clause
     */
    public function withADistanceBetween(int $min, int $max): Clause;

    /**
     * Define the minimum deepness of the relationship
     *
     * @param int $distance
     *
     * @throws LogicException If no relationship in the path
     *
     * @return Clause
     */
    public function withADistanceOfAtLeast(int $distance): Clause;

    /**
     * Define the maximum deepness of the relationship
     *
     * @param int $distance
     *
     * @throws LogicException If no relationship in the path
     *
     * @return Clause
     */
    public function withADistanceOfAtMost(int $distance): Clause;

    /**
     * Define any deepness of the relationship
     *
     * @param int $distance
     *
     * @throws LogicException If no relationship in the path
     *
     * @return Clause
     */
    public function withAnyDistance(): Clause;
}
