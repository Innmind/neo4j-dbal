<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

interface Clause
{
    /**
     * Return the line identifier of the clause (ie: MATCH, WHERE, SET, etc...)
     *
     * @return string
     */
    public function identifier(): string;

    /**
     * Return the cypher representation of the clause
     *
     * @return string
     */
    public function __toString(): string;
}
