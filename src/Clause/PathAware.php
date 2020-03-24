<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Exception\LogicException,
};

interface PathAware extends Parametrable
{
    /**
     * Link the currently matched node to another node
     */
    public function linkedTo(string $variable = null, string ...$labels): Clause;

    /**
     * Type the last connection
     *
     * @param 'both'|'left'|'right' $direction
     */
    public function through(
        string $variable = null,
        string $type = null,
        string $direction = 'both'
    ): Clause;

    /**
     * Specify a property to be matched
     */
    public function withProperty(string $property, string $cypher): Clause;

    /**
     * Define the deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOf(int $distance): Clause;

    /**
     * Define the deepness range of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceBetween(int $min, int $max): Clause;

    /**
     * Define the minimum deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOfAtLeast(int $distance): Clause;

    /**
     * Define the maximum deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOfAtMost(int $distance): Clause;

    /**
     * Define any deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withAnyDistance(): Clause;
}
