<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause;
use Innmind\Immutable\Map;

interface Parametrable
{
    /**
     * Add a parameter to the element
     *
     * @param string $key
     * @param mixed $value
     *
     * @return Clause
     */
    public function withParameter(string $key, $value): Clause;

    /**
     * Check if the element has parameters
     *
     * @return bool
     */
    public function hasParameters(): bool;

    /**
     * Return the list of parameters
     *
     * @return Map<string, Parameter>
     */
    public function parameters(): Map;
}
