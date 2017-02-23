<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Immutable\MapInterface;

interface ParametrableInterface
{
    /**
     * Add a parameter to the element
     *
     * @param string $key
     * @param mixed $value
     *
     * @return ClauseInterface
     */
    public function withParameter(string $key, $value): ClauseInterface;

    /**
     * Check if the element has parameters
     *
     * @return bool
     */
    public function hasParameters(): bool;

    /**
     * Return the list of parameters
     *
     * @return MapInterface<string, Parameter>
     */
    public function parameters(): MapInterface;
}
