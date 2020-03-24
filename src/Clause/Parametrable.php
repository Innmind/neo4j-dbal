<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Query\Parameter,
};
use Innmind\Immutable\Map;

interface Parametrable
{
    /**
     * Add a parameter to the element
     *
     * @param mixed $value
     */
    public function withParameter(string $key, $value): Clause;
    public function hasParameters(): bool;

    /**
     * @return Map<string, Parameter>
     */
    public function parameters(): Map;
}
